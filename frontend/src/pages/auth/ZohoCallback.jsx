// src/pages/ZohoCallback.jsx
import React, { useEffect } from "react";
import { useSearchParams, useNavigate } from "react-router-dom";
import { useAuthStore } from "@/store/useAuthStore";

const ZohoCallback = () => {
    const [params] = useSearchParams();
    const navigate = useNavigate();
    const setZohoToken = useAuthStore((state) => state.setZohoToken);

    useEffect(() => {
        const token = params.get("token");
        if (token) {
            setZohoToken(token);
            navigate("/dashboard"); // redirect to your app
        } else {
            navigate("/login");
        }
    }, []);

    return (
        <div className="flex h-screen items-center justify-center text-gray-600">
            Verifying Zoho Login...
        </div>
    );
};

export default ZohoCallback;
