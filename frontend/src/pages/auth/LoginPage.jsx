import React from "react";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Loader2, Plug } from "lucide-react";
import { useAuthStore } from "@/store/useAuthStore";

const LoginPage = () => {
    const { loginWithZoho, loading } = useAuthStore();

    return (
        <div className="min-h-screen flex items-center justify-center p-6  ">
            <Card className="w-full max-w-md shadow-xl border rounded-2xl bg-white">
                <CardHeader>
                    <CardTitle className="text-center text-2xl font-bold">
                        Login Through Zoho Account
                    </CardTitle>
                </CardHeader>
                <CardContent className="flex flex-col items-center space-y-6">
                    <Plug className="h-10 w-10 text-gray-500" />
                    <p className="text-center text-gray-700">
                        Click below to login through your Zoho account using OAuth 2.0.
                    </p>
                    <Button
                        onClick={loginWithZoho}
                        disabled={loading}
                        className="w-full bg-primary text-white hover:bg-primary/90 font-medium rounded-lg py-2 transition-all duration-200"
                    >
                        {loading ? (
                            <>
                                <Loader2 className="mr-2 h-4 w-4 animate-spin" /> Redirecting...
                            </>
                        ) : (
                            "Login with Zoho"
                        )}
                    </Button>
                </CardContent>
            </Card>
        </div>
    );
};

export default LoginPage;
