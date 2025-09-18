"use client";
import React from "react";
import { Question, Answer } from "@/types";

interface MultipleChoiceQuestionProps {
  question: Question;
  questionId: number;
  answer?: Answer;
  onAnswerChange: (questionId: number, answer: Answer) => void;
  fontSize: number;
}

const MultipleChoiceQuestion: React.FC<MultipleChoiceQuestionProps> = ({
  question,
  questionId,
  answer,
  onAnswerChange,
  fontSize,
}) => {
  const handleAnswerSelect = (optionId: string) => {
    onAnswerChange(questionId, {
      type: "single",
      value: optionId,
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
              type="radio"
              id={`option-${questionId}-${option.id}`}
              name={`question-${questionId}`}
              checked={answer?.value === option.id}
              onChange={() => handleAnswerSelect(option.id)}
              className="sr-only peer"
            />
            <div className="w-5 h-5 rounded-full border-2 border-blue-500 peer-checked:bg-blue-500 peer-checked:border-blue-600 transition-colors flex items-center justify-center">
              {answer?.value === option.id && (
                <div className="w-2 h-2 bg-white rounded-full"></div>
              )}
            </div>
          </div>

          <label
            htmlFor={`option-${questionId}-${option.id}`}
            className="flex-1 cursor-pointer group-hover:text-blue-600 transition-colors"
            style={{ fontSize: `${fontSize}px` }}
            onClick={() => handleAnswerSelect(option.id)}
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

export default MultipleChoiceQuestion;
