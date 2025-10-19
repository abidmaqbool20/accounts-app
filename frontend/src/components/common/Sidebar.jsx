import { useNavigate } from "react-router-dom";
import {
    LayoutDashboard,
    Calculator,
    Contact,
    Blocks,
    LogOut,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { ScrollArea } from "@/components/ui/scroll-area";
import { Separator } from "@/components/ui/separator";
import { useAuthStore } from "@/store/useAuthStore";
import { cn } from "@/lib/utils";

export function Sidebar() {
    const { logout } = useAuthStore();
    const navigate = useNavigate();

    const menuItems = [
        { label: "Dashboard", icon: LayoutDashboard, path: "/dashboard" },
        { label: "Accounts", icon: Calculator, path: "/accounts" },
        { label: "Contacts", icon: Contact, path: "/contacts" },
        { label: "Expenses", icon: Blocks, path: "/expenses" },
    ];

    return (
        <aside
            className={cn(
                "flex flex-col h-screen w-64 border-r border-gray-100 bg-white shadow-sm transition-all duration-200"
            )}
        >
            {/* Header */}
            <div className="px-4 py-4 border-b border-gray-200">
                <h1 className="text-2xl font-bold text-primary tracking-tight">Tmam</h1>
            </div>

            {/* Navigation */}
            <ScrollArea className="flex-1 px-3 py-4">
                <nav className="space-y-2">
                    {menuItems.map(({ label, icon: Icon, path }) => (
                        <Button
                            key={label}
                            variant="ghost"
                            className="w-full justify-start text-gray-700 hover:text-primary hover:bg-primary/10"
                            onClick={() => navigate(path)}
                        >
                            <Icon className="h-4 w-4 mr-2" /> {label}
                        </Button>
                    ))}
                </nav>
            </ScrollArea>

            <Separator className="my-4" />

            {/* Footer / Logout */}
            <div className="p-4">
                <Button
                    variant="destructive"
                    className="w-full justify-start"
                    onClick={logout}
                >
                    <LogOut className="h-4 w-4 mr-2" /> Logout
                </Button>
            </div>
        </aside>
    );
}
