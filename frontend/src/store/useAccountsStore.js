import { create } from "zustand";
import { persist } from "zustand/middleware";
import httpClient from "@/lib/axios";
import { useAuthStore } from "@/store/useAuthStore";

export const useAccountsStore = create(
    persist(
        (set, get) => ({
            chartOfAccounts: [],
            pagination: null,
            currentPage: 1,
            loading: false,

            setChartOfAccounts: (data) => set({ chartOfAccounts: data }),
            setLoading: (loading) => set({ loading }),
            setCurrentPage: (page) => set({ currentPage: page }),

            fetchChartOfAccounts: async (page = 1) => {
                set({ loading: true });
                try {
                    const { zohoToken } = useAuthStore.getState();
                    if (!zohoToken) throw new Error("Missing Zoho token");

                    const res = await httpClient.get(`/chart-of-accounts?page=${page}`, {
                        headers: { Authorization: `Bearer ${zohoToken}` },
                    });

                    const paginated = res.data.data;
                    const accounts = paginated.data || [];

                    set({
                        chartOfAccounts: accounts,
                        pagination: {
                            current_page: paginated.current_page,
                            last_page: paginated.last_page,
                            total: paginated.total,
                            links: paginated.links,
                            next_page_url: paginated.next_page_url,
                            prev_page_url: paginated.prev_page_url,
                        },
                        currentPage: page,
                    });

                    return accounts;
                } catch (error) {
                    throw error.response?.data?.message
                        ? new Error(error.response.data.message)
                        : error;
                } finally {
                    set({ loading: false });
                }
            },

            // 🔹 Sync accounts from Zoho then refresh local
            syncChartOfAccounts: async () => {
                set({ loading: true });
                try {
                    const { zohoToken } = useAuthStore.getState();
                    if (!zohoToken) throw new Error("Missing Zoho token");

                    await httpClient.get("/zoho/chart-of-accounts", {
                        headers: { Authorization: `Bearer ${zohoToken}` },
                    });

                    await get().fetchChartOfAccounts(1);
                } catch (error) {
                    throw error.response?.data?.message
                        ? new Error(error.response.data.message)
                        : error;
                } finally {
                    set({ loading: false });
                }
            },
        }),
        { name: "zoho-accounts-storage" }
    )
);
