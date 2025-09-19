"use client";
import React from "react";
import { GraduationCap } from "lucide-react";

const Header: React.FC = () => {
  return (
    <div
      className="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-700 to-blue-600 text-white h-20"
      style={{
        backgroundImage: `url('/bg.png')`,
        backgroundSize: "cover",
        backgroundRepeat: "no-repeat",
      }}
    >
      <div className="container mx-auto px-6 py-3 h-full">
        <div className="flex justify-between items-center h-full">
          <div className="flex items-center gap-4">
            <img src="/logo.png" alt="Kemdikbud Logo" className="h-12 w-12" />
            <div>
              <div className="font-bold text-lg">Ujian Smkmuhkandanghaur</div>
              <div className="text-sm opacity-90">SMK PK</div>
            </div>
          </div>

          <div className="flex items-center gap-3">
            <div className="text-right text-sm">
              <div>P130100230 - Manta</div>
            </div>
            <div className="bg-blue-400 p-2 rounded">
              <GraduationCap className="w-5 h-5 transform scale-x-[-1]" />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Header;
