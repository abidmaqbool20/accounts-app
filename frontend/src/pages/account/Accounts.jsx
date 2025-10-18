import React, { useEffect } from "react";
import { useQuery } from "@tanstack/react-query";
import { useZohoStore } from "@/store/useZohoStore";
import httpClient from "@/lib/axios";

import {
    Table,
    TableHeader,
    TableRow,
    TableHead,
    TableBody,
    TableCell,
} from "@/components/ui/table";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";

const Accounts = () => {
    const { chartOfAccounts, setChartOfAccounts } = useZohoStore();

    const { data, isLoading, error } = useQuery({
        queryKey: ["chart-of-accounts"],
        queryFn: async () => {
            const res = await httpClient.get("/zoho/chart-of-accounts");
            return res.data;
        },
    });

    useEffect(() => {
        if (data) setChartOfAccounts(data);
    }, [data, setChartOfAccounts]);

    if (isLoading)
        return (
            <Card className="mt-6">
                <CardHeader>
                    <CardTitle>Chart of Accounts</CardTitle>
                </CardHeader>
                <CardContent className="space-y-3">
                    <Skeleton className="h-8 w-full" />
                    <Skeleton className="h-8 w-full" />
                    <Skeleton className="h-8 w-full" />
                </CardContent>
            </Card>
        );

    if (error)
        return (
            <p className="text-red-500 text-sm mt-4">
                Failed to load accounts. Please try again.
            </p>
        );

    return (
        <Card className="mt-6">
            <CardHeader>
                <CardTitle>Chart of Accounts</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Account Name</TableHead>
                            <TableHead>Type</TableHead>
                            <TableHead>Code</TableHead>
                            <TableHead>Balance</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {chartOfAccounts?.map((acc) => (
                            <TableRow key={acc.id}>
                                <TableCell>{acc.name}</TableCell>
                                <TableCell>{acc.type}</TableCell>
                                <TableCell>{acc.code || "-"}</TableCell>
                                <TableCell>{acc.balance ?? "0.00"}</TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    );
};

export default Accounts;
