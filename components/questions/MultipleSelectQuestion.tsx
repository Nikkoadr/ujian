"use client";
import React from "react";
import { Question, Answer } from "@/types";

interface MultipleSelectQuestionProps {
  question: Question;
  questionId: number;
  answer?: Answer;
  onAnswerChange: (questionId: number, answer: Answer) => void;
  fontSize: number;
}

const MultipleSelectQuestion: React.FC<MultipleSelectQuestionProps> = ({
  question,
  questionId,
  answer,
  onAnswerChange,
  fontSize,
}) => {
  const selectedOptions = (answer?.value as string[]) || [];

  const handleAnswerToggle = (optionId: string) => {
    const currentSelected = [...selectedOptions];
    const index = currentSelected.indexOf(optionId);

    if (index > -1) {
      currentSelected.splice(index, 1);
    } else {
      currentSelected.push(optionId);
    }

    onAnswerChange(questionId, {
      type: "multiple",
      value: currentSelected,
    });
  };

  return (
    <div className="space-y-3">
      {question.options?.map((option) => (
        <div
          key={option.id}
          className="flex items-start gap-3 group cursor-pointer"
        >
          <div className="relative mt-1">
            <input
              type="checkbox"
              id={`option-${questionId}-${option.id}`}
              checked={selectedOptions.includes(option.id)}
              onChange={() => handleAnswerToggle(option.id)}
              className="sr-only peer"
            />
            <div className="w-5 h-5 rounded border-2 border-blue-500 peer-checked:bg-blue-500 peer-checked:border-blue-600 transition-colors flex items-center justify-center">
              {selectedOptions.includes(option.id) && (
                <svg
                  className="w-3 h-3 text-white"
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
          </div>

          <label
            htmlFor={`option-${questionId}-${option.id}`}
            className="flex-1 cursor-pointer group-hover:text-blue-600 transition-colors"
            style={{ fontSize: `${fontSize}px` }}
            onClick={() => handleAnswerToggle(option.id)}
          >
            {option.image ? (
              <img src={option.image} alt="" className="max-w-full h-auto" />
            ) : (
              <span dangerouslySetInnerHTML={{ __html: option.text }} />
            )}
          </label>
        </div>
      ))}
    </div>
  );
};

export default MultipleSelectQuestion;
