import {
    Bell,
    Settings,
    Menu,
    User,
    LogOut,
} from "lucide-react";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { useAuthStore } from "@/store/useAuthStore";
import { Separator } from "@/components/ui/separator";

export function Navbar() {
    const { logout } = useAuthStore();

    return (
        <header className="sticky top-0 z-40 w-full border-b border-amber-50 bg-white shadow-sm">
            <div className="flex h-16 items-center justify-between px-6">
                {/* Left section (Logo + Title) */}
                <div className="flex items-center gap-3">
                    <Button variant="ghost" size="icon" className="md:hidden">
                        <Menu className="h-5 w-5" />
                    </Button>
                    <h1 className="text-xl font-semibold text-gray-800 tracking-tight">
                        Zoho Integration Dashboard
                    </h1>
                </div>

                {/* Right section (Notifications + User Menu) */}
                <div className="flex items-center gap-3">
                    <Button variant="ghost" size="icon" className="relative">
                        <Bell className="h-5 w-5 text-gray-600" />
                        <span className="absolute top-1 right-1  h-2 w-2 bg-red-500 rounded-full" />
                    </Button>

                    <Separator orientation="vertical" className="h-6" />

                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button
                                variant="ghost"
                                className="flex items-center space-x-2 px-2"
                            >
                                <Avatar className="h-8 w-8">
                                    <AvatarImage src="/avatar.png" alt="User avatar" />
                                    <AvatarFallback>TM</AvatarFallback>
                                </Avatar>
                                <span className="hidden md:inline text-sm font-medium text-gray-700">
                                    Tmam
                                </span>
                            </Button>
                        </DropdownMenuTrigger>

                        <DropdownMenuContent align="end" className="w-48">
                            <DropdownMenuLabel>My Account</DropdownMenuLabel>
                            <DropdownMenuSeparator />

                            <DropdownMenuItem onClick={logout} className="text-red-600">
                                <LogOut className="h-4 w-4 mr-2" /> Logout
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </header>
    );
}
