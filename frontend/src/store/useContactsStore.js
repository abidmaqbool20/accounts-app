import { create } from "zustand";
import { persist } from "zustand/middleware";
import httpClient from "@/lib/axios";
import { useAuthStore } from "@/store/useAuthStore";

export const useContactsStore = create(
    persist(
        (set, get) => ({
            contacts: [],
            pagination: null,
            currentPage: 1,
            loading: false,

            setContacts: (data) => set({ contacts: data }),
            setLoading: (loading) => set({ loading }),
            setCurrentPage: (page) => set({ currentPage: page }),

            // 🔹 Fetch contacts (throws on error)
            fetchContacts: async (page = 1) => {
                set({ loading: true });
                try {
                    const { zohoToken } = useAuthStore.getState();
                    if (!zohoToken) throw new Error("Missing Zoho token");

                    const res = await httpClient.get(`/contacts?page=${page}`, {
                        headers: { Authorization: `Bearer ${zohoToken}` },
                    });

                    const paginated = res.data.data;
                    const contacts = paginated.data || [];

                    set({
                        contacts,
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

                    return contacts;
                } catch (error) {
                    throw error.response?.data?.message
                        ? new Error(error.response.data.message)
                        : error;
                } finally {
                    set({ loading: false });
                }
            },

            // 🔹 Sync contacts (throws on error)
            syncContacts: async () => {
                set({ loading: true });
                try {
                    const { zohoToken } = useAuthStore.getState();
                    if (!zohoToken) throw new Error("Missing Zoho token");

                    await httpClient.get("/zoho/contacts", {
                        headers: { Authorization: `Bearer ${zohoToken}` },
                    });

                    await get().fetchContacts(1);
                } catch (error) {
                    throw error.response?.data?.message
                        ? new Error(error.response.data.message)
                        : error;
                } finally {
                    set({ loading: false });
                }
            },
        }),
        { name: "zoho-contacts-storage" }
    )
);
