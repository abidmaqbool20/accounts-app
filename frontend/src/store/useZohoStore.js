import { create } from "zustand";
import { persist } from "zustand/middleware";
import httpClient from "@/lib/axios";

export const useZohoStore = create(
    persist(
        (set, get) => ({
            // ===== STATE =====
            zohoConnectToken: null,
            chartOfAccounts: [],
            contacts: [],
            loading: false,

            // ===== SETTERS =====
            setZohoConnectToken: (token) => set({ zohoConnectToken: token }),
            setChartOfAccounts: (data) => set({ chartOfAccounts: data }),
            setContacts: (data) => set({ contacts: data }),

            // ===== ACTIONS =====
            connectZoho: async () => {
                try {
                    set({ loading: true });
                    const res = await httpClient.get("/zoho/connect/url");
                    if (res.data?.auth_url) {
                        window.location.href = res.data.auth_url; // redirect to Zoho
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
                set({ zohoConnectToken: token });
            },

            fetchChartOfAccounts: async () => {
                try {
                    const { zohoConnectToken } = get();
                    if (!zohoConnectToken) throw new Error("No Zoho token found");
                    const res = await httpClient.get("/zoho/books/accounts", {
                        headers: { Authorization: `Bearer ${zohoConnectToken}` },
                    });
                    set({ chartOfAccounts: res.data || [] });
                } catch (error) {
                    console.error("Error fetching chart of accounts:", error);
                }
            },

            fetchContacts: async () => {
                try {
                    const { zohoConnectToken } = get();
                    if (!zohoConnectToken) throw new Error("No Zoho token found");
                    const res = await httpClient.get("/zoho/books/contacts", {
                        headers: { Authorization: `Bearer ${zohoConnectToken}` },
                    });
                    set({ contacts: res.data || [] });
                } catch (error) {
                    console.error("Error fetching contacts:", error);
                }
            },

            disconnectZoho: () => {
                set({ zohoConnectToken: null, chartOfAccounts: [], contacts: [] });
            },
        }),
        { name: "zoho-storage" } // persist store
    )
);
