import { create } from "zustand";
import { persist } from "zustand/middleware";
import httpClient from "@/lib/axios";
import { useAuthStore } from "@/store/useAuthStore";

export const useExpensesStore = create(
    persist(
        (set, get) => ({
            expenses: [],
            pagination: null,
            currentPage: 1,
            loading: false,

            setExpenses: (data) => set({ expenses: data }),
            setLoading: (loading) => set({ loading }),
            setCurrentPage: (page) => set({ currentPage: page }),

            /**
             * 🔹 Fetch paginated expenses from backend
             */
            fetchExpenses: async (page = 1) => {
                set({ loading: true });
                try {
                    const { zohoToken } = useAuthStore.getState();
                    if (!zohoToken) throw new Error("Missing Zoho token");

                    const res = await httpClient.get(`/expenses?page=${page}`, {
                        headers: { Authorization: `Bearer ${zohoToken}` },
                    });

                    const paginated = res.data.data;
                    const expenses = paginated.data || [];

                    set({
                        expenses,
                        pagination: {
                            current_page: paginated.current_page,
                            last_page: paginated.last_page,
                            total: paginated.total,
                            next_page_url: paginated.next_page_url,
                            prev_page_url: paginated.prev_page_url,
                        },
                        currentPage: page,
                    });

                    return expenses;
                } catch (error) {
                    throw error.response?.data?.message
                        ? new Error(error.response.data.message)
                        : error;
                } finally {
                    set({ loading: false });
                }
            },

            /**
             * 🔄 Sync expenses from Zoho Books and refresh local DB
             */
            syncExpenses: async () => {
                set({ loading: true });
                try {
                    const { zohoToken } = useAuthStore.getState();
                    if (!zohoToken) throw new Error("Missing Zoho token");

                    // Hit your backend endpoint that triggers Zoho sync
                    await httpClient.get("zoho/expenses", {
                        headers: { Authorization: `Bearer ${zohoToken}` },
                    });

                    // Refresh local data
                    await get().fetchExpenses(1);
                } catch (error) {
                    throw error.response?.data?.message
                        ? new Error(error.response.data.message)
                        : error;
                } finally {
                    set({ loading: false });
                }
            },
        }),
        { name: "zoho-expenses-storage" }
    )
);
