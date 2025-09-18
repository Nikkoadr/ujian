"use client";
import React from "react";
import { Clock, CheckCircle } from "lucide-react";

interface FinishModalProps {
  isOpen: boolean;
  onClose: () => void;
  onFinish: () => void;
  totalQuestions: number;
  answeredCount: number;
  doubtfulCount: number;
  timeLeft: number; // detik
}

const FinishModal: React.FC<FinishModalProps> = ({
  isOpen,
  onClose,
  onFinish,
  totalQuestions,
  answeredCount,
  doubtfulCount,
  timeLeft,
}) => {
  if (!isOpen) return null;

  const handleFinishClick = () => {
    if (timeLeft > 300) {
      // lebih dari 5 menit
      if (
        confirm(
          `Waktu masih tersisa ${Math.floor(
            timeLeft / 60
          )} menit. Yakin ingin mengakhiri ujian?`
        )
      ) {
        onFinish();
      }
    } else {
      onFinish();
    }
  };

  return (
    <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg shadow-xl max-w-md w-full p-6 text-center">
        {/* Header */}
        <div className="flex flex-col items-center gap-2 mb-6">
          <Clock size={32} className="text-blue-500" />
          <h3 className="text-xl font-semibold">Selesaikan Ujian</h3>
        </div>

        {/* Status */}
        <div className="mb-6">
          <CheckCircle size={64} className="mx-auto text-green-500 mb-4" />
          <h4 className="text-lg font-semibold mb-2">Ujian Telah Berakhir</h4>
          <p className="text-gray-600">
            Jawaban Anda akan diproses secara otomatis setelah menekan tombol
            "Selesaikan Ujian".
          </p>
        </div>

        {/* Statistik */}
        <div className="grid grid-cols-3 gap-4 mb-6 text-center">
          <div className="p-4 bg-blue-50 rounded-lg">
            <div className="text-2xl font-bold text-blue-600">
              {totalQuestions}
            </div>
            <div className="text-sm text-gray-600">Total Soal</div>
          </div>
          <div className="p-4 bg-green-50 rounded-lg">
            <div className="text-2xl font-bold text-green-600">
              {answeredCount}
            </div>
            <div className="text-sm text-gray-600">Terjawab</div>
          </div>
          <div className="p-4 bg-yellow-50 rounded-lg">
            <div className="text-2xl font-bold text-yellow-600">
              {doubtfulCount}
            </div>
            <div className="text-sm text-gray-600">Ragu-ragu</div>
          </div>
        </div>

        {/* Tombol */}
        <button
          onClick={handleFinishClick}
          className="w-full bg-blue-600 hover:bg-blue-500 text-white py-2 rounded-md font-semibold transition-colors mb-2"
        >
          Selesaikan Ujian
        </button>

        <button
          onClick={onClose}
          className="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 rounded-md font-semibold transition-colors"
        >
          Batal
        </button>
      </div>
    </div>
  );
};

export default FinishModal;
