import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { useAuthStore } from "@/store/useAuthStore";

import LoginPage from "@/pages/auth/LoginPage";
import Dashboard from "@/pages/account/Dashboard";
import Accounts from "@/pages/account/Accounts";
import Contacts from "@/pages/account/Contacts";
import Expenses from "@/pages/account/Expenses";
import Layout from "@/app/layout";
import ZohoCallback from "@/pages/auth/ZohoCallback";

export default function AppRoutes() {
    const { zohoToken } = useAuthStore();

    return (
        <BrowserRouter>
            <Routes>
                {/* ✅ Always allow Zoho OAuth redirect callback to work */}
                <Route path="/zoho/callback" element={<ZohoCallback />} />

                {/* ✅ If not logged in, go to Login */}
                {!zohoToken ? (
                    <Route path="*" element={<LoginPage />} />
                ) : (
                    /* ✅ Authenticated routes */
                    <Route element={<Layout />}>
                        <Route path="/" element={<Dashboard />} />
                        <Route path="/dashboard" element={<Dashboard />} />
                        <Route path="/accounts" element={<Accounts />} />
                        <Route path="/contacts" element={<Contacts />} />
                        <Route path="/expenses" element={<Expenses />} />
                        <Route path="*" element={<Navigate to="/" />} />
                    </Route>
                )}
            </Routes>
        </BrowserRouter>
    );
}
