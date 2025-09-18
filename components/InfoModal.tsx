"use client";
import React from "react";
import { X } from "lucide-react";

interface InfoModalProps {
  isOpen: boolean;
  onClose: () => void;
}

const InfoModal: React.FC<InfoModalProps> = ({ isOpen, onClose }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg shadow-xl max-w-lg w-full">
        {/* Header */}
        <div className="flex items-center justify-between p-4 border-b bg-gray-100">
          <h3 className="text-lg font-semibold text-gray-800">
            Informasi Soal
          </h3>
          <button
            onClick={onClose}
            className="text-gray-500 hover:text-gray-700 transition-colors"
          >
            <X className="w-6 h-6" />
          </button>
        </div>

        {/* Body */}
        <div className="p-6">
          <div className="space-y-4 text-sm text-gray-700">
            <div>
              <h4 className="font-semibold mb-2">Petunjuk Umum:</h4>
              <ul className="list-disc list-inside space-y-1">
                <li>Baca setiap soal dengan teliti sebelum menjawab</li>
                <li>Pilih jawaban yang paling tepat</li>
                <li>Gunakan tombol "Ragu-ragu" jika tidak yakin</li>
                <li>Periksa kembali jawaban sebelum mengakhiri ujian</li>
              </ul>
            </div>

            <div>
              <h4 className="font-semibold mb-2">Navigasi:</h4>
              <ul className="list-disc list-inside space-y-1">
                <li>Pakai tombol "Soal sebelumnya" & "Soal berikutnya"</li>
                <li>Klik "Daftar Soal" untuk melompat ke soal tertentu</li>
                <li>Soal terjawab ditandai warna gelap</li>
                <li>Soal ragu-ragu ditandai warna kuning</li>
              </ul>
            </div>

            <div>
              <h4 className="font-semibold mb-2">Waktu:</h4>
              <p>
                Perhatikan sisa waktu di bagian atas. Ujian akan berakhir
                otomatis ketika waktu habis.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default InfoModal;
