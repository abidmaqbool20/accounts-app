import React, { useState } from "react";
import { Sidebar } from "@/components/common/Sidebar";
import { Navbar } from "@/components/common/Navbar";
import { Sheet, SheetTrigger, SheetContent } from "@/components/ui/sheet";
import { Button } from "@/components/ui/button";
import { Menu } from "lucide-react";
import { cn } from "@/lib/utils";
import { Outlet } from "react-router-dom";

export default function Layout() {
  const [open, setOpen] = useState(false);

  return (
    <div
      className={cn(
        "min-h-screen w-full flex flex-col lg:flex-row overflow-hidden"
      )}
    >
      {/* ======= SIDEBAR ======= */}
      <div className="hidden lg:block w-64">
        <div className="sticky top-0 h-screen border-r bg-white/90 backdrop-blur-sm shadow-sm">
          <Sidebar />
        </div>
      </div>

      {/* ======= MOBILE SIDEBAR ======= */}
      <Sheet open={open} onOpenChange={setOpen}>
        <SheetTrigger asChild>
          <Button
            variant="ghost"
            size="icon"
            className="lg:hidden fixed top-4 left-4 z-50 bg-white/70 backdrop-blur-sm shadow-sm"
          >
            <Menu className="h-5 w-5" />
          </Button>
        </SheetTrigger>
        <SheetContent side="left" className="p-0 w-64 border-none shadow-xl">
          <Sidebar />
        </SheetContent>
      </Sheet>

      {/* ======= MAIN AREA ======= */}
      <div className="flex-1 flex flex-col relative">
        {/* Navbar (sticky top) */}
        <div className="sticky top-0 z-40">
          <Navbar />
        </div>

        {/* Main Content */}
        <main
          className={cn(
            "flex-1 overflow-y-auto p-2 md:p-2 overflow-auto min-h-[90vh] max-h-[90vh]",
            "bg-gradient-to-b from-white/70 to-white/40 backdrop-blur-md",
            "rounded-t-3xl lg:rounded-none"
          )}
        >
          <Outlet />
        </main>

      </div>
    </div>
  );
}
