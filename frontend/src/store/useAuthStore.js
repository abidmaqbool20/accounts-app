// src/store/useAuthStore.js
import { create } from "zustand";
import { persist } from "zustand/middleware";
import httpClient from "@/lib/axios";

export const useAuthStore = create(
    persist(
        (set) => ({
            zohoToken: null,
            loading: false,

            setZohoToken: (token) => set({ zohoToken: token }),
            logout: () => set({ zohoToken: null }),

            loginWithZoho: async () => {
                try {
                    set({ loading: true });
                    const res = await httpClient.get("/zoho/auth/url");
                    if (res.data?.auth_url) {
                        window.location.href = res.data.auth_url;
                    } else {
                        alert("Unable to get authorization URL");
                    }
                } catch (error) {
                    console.error("Zoho login error:", error);
                    alert("Error generating Zoho login URL");
                } finally {
                    set({ loading: false });
                }
            },
        }),
        { name: "auth-storage" }
    )
);
