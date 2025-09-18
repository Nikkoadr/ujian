"use client";
import React from "react";
import { ChevronLeft, ChevronRight } from "lucide-react";

interface QuestionNavigationProps {
  currentQuestion: number;
  totalQuestions: number;
  isUncertain: boolean;
  onPrevious: () => void;
  onNext: () => void;
  onUncertainChange: (uncertain: boolean) => void;
}

const QuestionNavigation: React.FC<QuestionNavigationProps> = ({
  currentQuestion,
  totalQuestions,
  isUncertain,
  onPrevious,
  onNext,
  onUncertainChange,
}) => {
  return (
    <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
      <button
        onClick={onPrevious}
        disabled={currentQuestion === 1}
        className="flex items-center gap-2 px-6 py-3 bg-red-500 text-white rounded-full font-medium hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors min-w-[180px] justify-center"
      >
        <ChevronLeft className="w-5 h-5" />
        <span className="hidden sm:inline">Soal sebelumnya</span>
        <span className="sm:hidden">Sebelum</span>
      </button>

      <label className="flex items-center gap-3 px-6 py-3 bg-yellow-500 text-white rounded-full font-medium cursor-pointer hover:bg-yellow-600 transition-colors min-w-[140px] justify-center">
        <input
          type="checkbox"
          checked={isUncertain}
          onChange={(e) => onUncertainChange(e.target.checked)}
          className="sr-only"
        />
        <div
          className={`w-4 h-4 rounded border-2 border-white flex items-center justify-center ${
            isUncertain ? "bg-white" : ""
          }`}
        >
          {isUncertain && (
            <svg
              className="w-3 h-3 text-yellow-500"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fillRule="evenodd"
                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                clipRule="evenodd"
              />
            </svg>
          )}
        </div>
        <span className="hidden sm:inline">Ragu-ragu</span>
        <span className="sm:hidden">Ragu</span>
      </label>

      <button
        onClick={onNext}
        className="flex items-center gap-2 px-6 py-3 bg-blue-500 text-white rounded-full font-medium hover:bg-blue-600 transition-colors min-w-[180px] justify-center"
      >
        <span className="hidden sm:inline">
          {currentQuestion === totalQuestions ? "Selesai" : "Soal berikutnya"}
        </span>
        <span className="sm:hidden">
          {currentQuestion === totalQuestions ? "Selesai" : "Berikut"}
        </span>
        <ChevronRight className="w-5 h-5" />
      </button>
    </div>
  );
};

export default QuestionNavigation;
