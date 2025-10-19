import React, { useEffect, useRef, useState } from "react";
import { useContactsStore } from "@/store/useContactsStore";
import toast from "react-hot-toast";
import {
    Table, TableHeader, TableRow, TableHead, TableBody, TableCell,
} from "@/components/ui/table";
import {
    Card, CardHeader, CardTitle, CardContent,
} from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

export default function ContactsTable() {
    const { contacts, pagination, loading, fetchContacts, syncContacts } =
        useContactsStore();

    const [currentPage, setCurrentPage] = useState(1);
    const lastFetchedPage = useRef(null);

    useEffect(() => {
        const loadContacts = async () => {
            try {
                if (lastFetchedPage.current !== currentPage) {
                    lastFetchedPage.current = currentPage;
                    await fetchContacts(currentPage);
                }
            } catch (error) {
                toast.error(error?.message || "Failed to load contacts.");
            }
        };
        loadContacts();
    }, [currentPage, fetchContacts]);

    const handleSync = async () => {
        toast.promise(
            syncContacts(),
            {
                loading: "Syncing contacts from Zoho...",
                success: "Contacts synced successfully!",
                error: (err) =>
                    err?.message || "Zoho sync failed. Please check connection.",
            }
        );
        lastFetchedPage.current = null;
        setCurrentPage(1);
    };

    const handlePageChange = (url) => {
        if (!url) return;
        const page = new URL(url).searchParams.get("page");
        if (page) setCurrentPage(Number(page));
    };

    if (loading) {
        return (
            <Card className="mt-6">
                <CardHeader>
                    <CardTitle>Contacts</CardTitle>
                </CardHeader>
                <CardContent className="space-y-3">
                    {[...Array(5)].map((_, i) => (
                        <Skeleton key={i} className="h-8 w-full" />
                    ))}
                </CardContent>
            </Card>
        );
    }

    return (
        <Card className="mt-6 shadow-md border border-gray-100 rounded-xl">
            <CardHeader>
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div className="flex items-center gap-3">
                        <CardTitle className="text-xl font-bold text-primary">
                            Contacts
                        </CardTitle>
                        <Button
                            onClick={handleSync}
                            variant="outline"
                            className="text-sm font-medium"
                            disabled={loading}
                        >
                            {loading ? "Syncing..." : "Sync Zoho Contacts"}
                        </Button>
                    </div>
                    <p className="text-sm text-muted-foreground">
                        {pagination?.total || contacts?.length || 0} total contacts
                    </p>
                </div>
            </CardHeader>

            <CardContent>
                <div className="rounded-lg border border-gray-100 overflow-hidden">
                    <Table>
                        <TableHeader className="bg-gray-50">
                            <TableRow>
                                <TableHead className="w-[30%]">Name</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Phone</TableHead>
                                <TableHead>Company</TableHead>
                                <TableHead className="text-right">Type</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {contacts?.length > 0 ? (
                                contacts.map((c) => (
                                    <TableRow
                                        key={c.id || c.contact_id}
                                        className="hover:bg-gray-50 transition-colors"
                                    >
                                        <TableCell className="flex items-center gap-2">
                                            <Avatar className="h-8 w-8">
                                                <AvatarFallback>
                                                    {c.contact_name?.[0]?.toUpperCase() || "?"}
                                                </AvatarFallback>
                                            </Avatar>
                                            <span className="font-medium text-gray-900">
                                                {c.contact_name || c.name || "—"}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            {c.contact_persons?.[0]?.email || c.email || "—"}
                                        </TableCell>
                                        <TableCell>
                                            {c.contact_persons?.[0]?.phone || c.phone || "—"}
                                        </TableCell>
                                        <TableCell>{c.company_name || c.company || "—"}</TableCell>
                                        <TableCell className="text-right">
                                            <Badge
                                                variant="secondary"
                                                className="capitalize bg-primary/10 text-primary border-none"
                                            >
                                                {c.contact_type || c.type || "—"}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                ))
                            ) : (
                                <TableRow>
                                    <TableCell
                                        colSpan={5}
                                        className="text-center py-6 text-sm text-gray-500"
                                    >
                                        No contacts found.
                                    </TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </div>

                {pagination && (
                    <div className="flex items-center justify-center gap-3 mt-5">
                        <Button
                            variant="outline"
                            size="sm"
                            disabled={!pagination.prev_page_url}
                            onClick={() => handlePageChange(pagination.prev_page_url)}
                        >
                            Previous
                        </Button>

                        <span className="text-sm text-gray-600">
                            Page {pagination.current_page} of {pagination.last_page}
                        </span>

                        <Button
                            variant="outline"
                            size="sm"
                            disabled={!pagination.next_page_url}
                            onClick={() => handlePageChange(pagination.next_page_url)}
                        >
                            Next
                        </Button>
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
