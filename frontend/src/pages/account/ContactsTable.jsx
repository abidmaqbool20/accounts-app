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
import { Avatar, AvatarFallback } from "@/components/ui/avatar";

const ContactsTable = () => {
    const { contacts, setContacts } = useZohoStore();

    const { data, isLoading, error } = useQuery({
        queryKey: ["contacts"],
        queryFn: async () => {
            const res = await httpClient.get("/zoho/contacts");
            return res.data;
        },
    });

    useEffect(() => {
        if (data) setContacts(data);
    }, [data, setContacts]);

    if (isLoading)
        return (
            <Card className="mt-6">
                <CardHeader>
                    <CardTitle>Contacts</CardTitle>
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
                Failed to load contacts. Please try again.
            </p>
        );

    return (
        <Card className="mt-6">
            <CardHeader>
                <CardTitle>Contacts</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Phone</TableHead>
                            <TableHead>Company</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {contacts?.map((c) => (
                            <TableRow key={c.id}>
                                <TableCell className="flex items-center gap-2">
                                    <Avatar>
                                        <AvatarFallback>
                                            {c.name?.[0]?.toUpperCase() || "?"}
                                        </AvatarFallback>
                                    </Avatar>
                                    {c.name}
                                </TableCell>
                                <TableCell>{c.email || "-"}</TableCell>
                                <TableCell>{c.phone || "-"}</TableCell>
                                <TableCell>{c.company_name || "-"}</TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    );
};

export default ContactsTable;
