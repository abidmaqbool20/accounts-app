import React, { useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import { LogIn, CheckCircle } from "lucide-react";
import { useZohoStore } from "@/store/useZohoStore";
import { useLocation, useNavigate } from "react-router-dom";

export default function Dashboard() {
    const { connectZoho, zohoConnectToken, loading, handleZohoConnectCallback } =
        useZohoStore();

    const location = useLocation();
    const navigate = useNavigate();

    // ✅ Step 1: Check for Zoho redirect params in URL
    useEffect(() => {
        const searchParams = new URLSearchParams(location.search);
        const connected = searchParams.get("zoho");
        const token = searchParams.get("token");

        if (connected === "connected" && token) {
            handleZohoConnectCallback(token); // store token in Zustand
            // Replace URL without query params
            navigate("/dashboard", { replace: true });
        }
    }, [location.search, handleZohoConnectCallback, navigate]);

    return (
        <div className="flex justify-center items-center min-h-[70vh]">
            <Card className="w-full max-w-md shadow-xl border border-gray-200 rounded-2xl bg-gradient-to-br from-white via-gray-50 to-gray-100">
                <CardHeader className="text-center">
                    <CardTitle className="text-2xl font-bold text-gray-800 flex flex-col items-center gap-2">
                        {zohoConnectToken ? (
                            <CheckCircle className="h-10 w-10 text-green-600" />
                        ) : (
                            <LogIn className="h-10 w-10 text-primary" />
                        )}
                        {zohoConnectToken ? "Zoho Connected" : "Connect to Zoho"}
                    </CardTitle>
                </CardHeader>
                <CardContent className="text-center space-y-6">
                    {!zohoConnectToken ? (
                        <>
                            <p className="text-gray-600">
                                Click below to securely connect your{" "}
                                <strong>Zoho Books</strong> account.
                            </p>

                            <Button
                                onClick={connectZoho}
                                className="w-full py-5 text-lg font-medium"
                                disabled={loading}
                            >
                                {loading ? "Connecting..." : "Connect with Zoho"}
                            </Button>
                        </>
                    ) : (
                        <p className="text-green-600 font-medium">
                            ✅ Your application is connected to Zoho Books.
                        </p>
                    )}
                </CardContent>
            </Card>
        </div>
    );
}
