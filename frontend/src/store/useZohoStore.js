import { create } from "zustand";
import { persist } from "zustand/middleware";
import httpClient from "@/lib/axios";
import { useAuthStore } from "@/store/useAuthStore";

export const useZohoStore = create(
    persist(
        (set, get) => ({
            zohoConnectToken: null,
            tokenIssuedAt: null,
            loading: false,

            setZohoConnectToken: (token) =>
                set({ zohoConnectToken: token, tokenIssuedAt: Date.now() }),

            setLoading: (loading) => set({ loading }),


            checkTokenExpiry: () => {
                const { zohoConnectToken, tokenIssuedAt } = get();
                if (!zohoConnectToken || !tokenIssuedAt) return;

                const now = Date.now();
                const oneHour = 60 * 60 * 1000; // 1 hour in ms

                if (now - tokenIssuedAt > oneHour) {
                    console.log("⏰ Zoho token expired — clearing store");
                    set({ zohoConnectToken: null, tokenIssuedAt: null });
                }
            },

            connectZoho: async () => {
                try {
                    set({ loading: true });
                    const { zohoToken } = useAuthStore.getState();
                    if (!zohoToken) throw new Error("Missing Zoho token");

                    const res = await httpClient.get("/zoho/connect/url", {
                        headers: { Authorization: `Bearer ${zohoToken}` },
                    });

                    if (res.data?.data?.auth_url) {
                        window.location.href = res.data.data.auth_url;
                    } else {
                        alert("Unable to get Zoho authorization URL.");
                    }
                } catch (error) {
                    console.error("Zoho connect error:", error);
                    alert("Error generating Zoho connect URL");
                } finally {
                    set({ loading: false });
                }
            },

            handleZohoConnectCallback: async (token) => {
                if (!token) return;
                set({ zohoConnectToken: token, tokenIssuedAt: Date.now() });
            },

            disconnectZoho: () => {
                set({ zohoConnectToken: null, tokenIssuedAt: null });
            },
        }),
        {
            name: "zoho-storage",

            onRehydrateStorage: () => (state) => {
                setTimeout(() => {
                    state?.checkTokenExpiry?.();
                }, 100);
            },
        }
    )
);
