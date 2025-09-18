"use client";
import React from "react";
import { X, Check } from "lucide-react";
import { Answer } from "@/types";

interface QuestionListModalProps {
  isOpen: boolean;
  onClose: () => void;
  totalQuestions: number;
  currentQuestion: number;
  answers: Record<number, Answer>;
  isUncertain: Record<number, boolean>;
  onQuestionSelect: (questionId: number) => void;
  getQuestionStatus: (
    questionId: number
  ) => "answered" | "uncertain" | "unanswered";
}

const QuestionListModal: React.FC<QuestionListModalProps> = ({
  isOpen,
  onClose,
  totalQuestions,
  currentQuestion,
  answers,
  isUncertain,
  onQuestionSelect,
  getQuestionStatus,
}) => {
  if (!isOpen) return null;

  const handleQuestionClick = (questionId: number) => {
    onQuestionSelect(questionId);
    onClose();
  };

  const getButtonClass = (questionId: number) => {
    const status = getQuestionStatus(questionId);
    const isCurrent = questionId === currentQuestion;

    let baseClass =
      "w-10 h-10 rounded font-medium text-sm transition-colors relative ";

    if (isCurrent) {
      baseClass += "bg-blue-500 text-white ";
    } else if (status === "uncertain") {
      baseClass += "bg-yellow-500 text-white ";
    } else if (status === "answered") {
      baseClass += "bg-gray-800 text-white ";
    } else {
      baseClass +=
        "bg-white border-2 border-gray-300 text-gray-700 hover:border-gray-400 ";
    }

    return baseClass;
  };

  return (
    <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[80vh] overflow-auto">
        {/* Header */}
        <div className="flex items-center justify-between p-4 border-b bg-gray-100">
          <h3 className="text-lg font-semibold text-gray-800">Daftar Soal</h3>
          <button
            onClick={onClose}
            className="text-gray-500 hover:text-gray-700 transition-colors"
          >
            <X className="w-6 h-6" />
          </button>
        </div>

        {/* Body */}
        <div className="p-6">
          <div className="grid grid-cols-5 gap-3">
            {Array.from({ length: totalQuestions }, (_, index) => {
              const questionId = index + 1;
              const hasAnswer = answers[questionId];

              return (
                <button
                  key={questionId}
                  onClick={() => handleQuestionClick(questionId)}
                  className={getButtonClass(questionId)}
                >
                  {questionId}
                  {hasAnswer && (
                    <div className="absolute -top-1 -right-1 w-4 h-4 bg-white border border-gray-800 rounded-full flex items-center justify-center">
                      <Check className="w-2 h-2 text-gray-800" />
                    </div>
                  )}
                </button>
              );
            })}
          </div>

          {/* Legend */}
          <div className="mt-6 flex flex-wrap items-center gap-4 text-sm text-gray-600 justify-center">
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 bg-gray-800 rounded"></div>
              <span>Sudah dijawab</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 bg-yellow-500 rounded"></div>
              <span>Ragu-ragu</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 bg-blue-500 rounded"></div>
              <span>Soal aktif</span>
            </div>
            <div className="flex items-center gap-2">
              <div className="w-4 h-4 bg-white border-2 border-gray-300 rounded"></div>
              <span>Belum dijawab</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default QuestionListModal;
