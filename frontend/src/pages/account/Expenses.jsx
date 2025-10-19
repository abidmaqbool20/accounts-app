import React, { useEffect, useRef, useState } from "react";
import { useExpensesStore } from "@/store/useExpensesStore";
import toast from "react-hot-toast";
import {
    Table, TableHeader, TableRow, TableHead, TableBody, TableCell,
} from "@/components/ui/table";
import {
    Card, CardHeader, CardTitle, CardContent,
} from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { format } from "date-fns";
import { Download } from "lucide-react";

export default function ExpensesTable() {
    const {
        expenses,
        pagination,
        loading,
        fetchExpenses,
        syncExpenses,
    } = useExpensesStore();

    const [currentPage, setCurrentPage] = useState(1);
    const lastFetchedPage = useRef(null);

    // Fetch expenses on mount or page change
    useEffect(() => {
        if (lastFetchedPage.current !== currentPage) {
            lastFetchedPage.current = currentPage;
            fetchExpenses(currentPage);
        }
    }, [currentPage, fetchExpenses]);

    // Sync button handler
    const handleSync = async () => {
        toast.promise(syncExpenses(), {
            loading: "Syncing expenses from Zoho...",
            success: "Expenses synced successfully!",
            error: (err) =>
                err?.message || "Zoho sync failed. Please check connection.",
        });
        lastFetchedPage.current = null;
        setCurrentPage(1);
    };

    const handlePageChange = (url) => {
        if (!url) return;
        const page = new URL(url).searchParams.get("page");
        if (page) setCurrentPage(Number(page));
    };

    const handleDownloadReceipt = (receipt) => {
        if (!receipt?.file_download_url) return toast.error("No file available.");
        const link = document.createElement("a");
        link.href = receipt.file_download_url;
        link.download = receipt.file_name;
        link.target = "_blank";
        link.click();
    };

    // Loading skeleton
    if (loading) {
        return (
            <Card className="mt-6">
                <CardHeader>
                    <CardTitle>Expenses</CardTitle>
                </CardHeader>
                <CardContent className="space-y-3">
                    {[...Array(5)].map((_, i) => (
                        <Skeleton key={i} className="h-8 w-full" />
                    ))}
                </CardContent>
            </Card>
        );
    }

    // ===== Main Table =====
    return (
        <Card className="mt-6 shadow-lg border border-gray-100 rounded-xl">
            <CardHeader>
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div className="flex items-center gap-3">
                        <CardTitle className="text-xl font-bold text-primary">
                            Expenses
                        </CardTitle>
                        <Button
                            onClick={handleSync}
                            variant="outline"
                            className="text-sm font-medium"
                            disabled={loading}
                        >
                            {loading ? "Syncing..." : "Sync Zoho Expenses"}
                        </Button>
                    </div>
                    <p className="text-sm text-muted-foreground">
                        {pagination?.total || expenses?.length || 0} total expenses
                    </p>
                </div>
            </CardHeader>

            <CardContent>
                <div className="rounded-lg border border-gray-100 overflow-hidden">
                    <Table>
                        <TableHeader className="bg-gray-50">
                            <TableRow>
                                <TableHead className="w-[12%]">Expense ID</TableHead>
                                <TableHead>Date</TableHead>
                                <TableHead>Account</TableHead>
                                <TableHead>Paid Through</TableHead>
                                <TableHead className="w-[15%]">Total</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Receipt</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody class="text-left">
                            {expenses?.length > 0 ? (
                                expenses.map((exp) => (
                                    <TableRow
                                        key={exp.id}
                                        className="hover:bg-gray-50 transition-colors"
                                    >
                                        <TableCell className="font-mono text-xs text-gray-800">
                                            {exp.expense_id}
                                        </TableCell>

                                        <TableCell>
                                            {exp.date
                                                ? format(new Date(exp.date), "dd MMM yyyy")
                                                : "—"}
                                        </TableCell>

                                        <TableCell className="text-sm font-medium text-gray-800">
                                            {exp.account_name || "—"}
                                        </TableCell>

                                        <TableCell className="text-gray-600">
                                            {exp.paid_through_account_name || "—"}
                                        </TableCell>

                                        <TableCell className="text-gray-900 font-semibold">
                                            {exp.total
                                                ? `${parseFloat(exp.total).toFixed(2)} ${exp.currency_code || ""
                                                }`
                                                : "—"}
                                        </TableCell>

                                        <TableCell>
                                            <Badge
                                                variant="secondary"
                                                className="capitalize bg-primary/10 text-primary border-none"
                                            >
                                                {exp.status || "—"}
                                            </Badge>
                                        </TableCell>

                                        <TableCell className="text-right">
                                            {exp.receipts?.length > 0 ? (
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    onClick={() =>
                                                        handleDownloadReceipt(exp.receipts[0])
                                                    }
                                                    className="flex items-center gap-1"
                                                >
                                                    <Download className="w-4 h-4" />
                                                    Download
                                                </Button>
                                            ) : (
                                                <span className="text-xs text-gray-400">No file</span>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                ))
                            ) : (
                                <TableRow>
                                    <TableCell
                                        colSpan={7}
                                        className="text-center py-6 text-sm text-gray-500"
                                    >
                                        No expenses found.
                                    </TableCell>
                                </TableRow>
                            )}
                        </TableBody>
                    </Table>
                </div>

                {/* ===== Pagination ===== */}
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
