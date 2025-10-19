import { Toaster } from "react-hot-toast";

export default function AppToaster() {
    return (
        <Toaster
            position="top-right"
            toastOptions={{
                style: {
                    background: "#fff",
                    color: "#333",
                    borderRadius: "8px",
                    boxShadow: "0 2px 10px rgba(0,0,0,0.1)",
                },
                success: {
                    iconTheme: {
                        primary: "#10b981",
                        secondary: "#fff",
                    },
                },
                error: {
                    iconTheme: {
                        primary: "#ef4444",
                        secondary: "#fff",
                    },
                },
            }}
        />
    );
}
