"use client";
import React from "react";
import { Question, Answer } from "@/types";

interface TrueFalseTableQuestionProps {
  question: Question;
  questionId: number;
  answer?: Answer;
  onAnswerChange: (questionId: number, answer: Answer) => void;
  fontSize: number;
}

const TrueFalseTableQuestion: React.FC<TrueFalseTableQuestionProps> = ({
  question,
  questionId,
  answer,
  onAnswerChange,
  fontSize,
}) => {
  const answers = (answer?.value as Record<string, string>) || {};

  const handleAnswerSelect = (statementId: string, value: string) => {
    const newAnswers = {
      ...answers,
      [statementId]: value,
    };

    onAnswerChange(questionId, {
      type: "complex",
      value: newAnswers,
    });
  };

  return (
    <div className="overflow-x-auto">
      <table className="w-full border border-gray-200 rounded-lg">
        <thead className="bg-gray-50">
          <tr>
            <th className="px-4 py-3 text-left font-medium text-gray-700 border-b">
              Pernyataan
            </th>
            <th className="px-4 py-3 text-center font-medium text-gray-700 border-b w-24">
              Benar
            </th>
            <th className="px-4 py-3 text-center font-medium text-gray-700 border-b w-24">
              Salah
            </th>
          </tr>
        </thead>
        <tbody>
          {question.statements?.map((statement, index) => (
            <tr
              key={statement.id}
              className={index % 2 === 0 ? "bg-white" : "bg-gray-50"}
            >
              <td className="px-4 py-3 border-b">
                <div style={{ fontSize: `${fontSize}px` }}>
                  {statement.image ? (
                    <img
                      src={statement.image}
                      alt=""
                      className="max-w-full h-auto"
                    />
                  ) : (
                    <span
                      dangerouslySetInnerHTML={{ __html: statement.text }}
                    />
                  )}
                </div>
              </td>
              <td className="px-4 py-3 text-center border-b">
                <div className="relative">
                  <input
                    type="radio"
                    id={`true-${questionId}-${statement.id}`}
                    name={`statement-${questionId}-${statement.id}`}
                    checked={answers[statement.id] === "true"}
                    onChange={() => handleAnswerSelect(statement.id, "true")}
                    className="sr-only peer"
                  />
                  <div
                    className="w-5 h-5 mx-auto rounded-full border-2 border-blue-500 peer-checked:bg-blue-500 peer-checked:border-blue-600 transition-colors flex items-center justify-center cursor-pointer"
                    onClick={() => handleAnswerSelect(statement.id, "true")}
                  >
                    {answers[statement.id] === "true" && (
                      <div className="w-2 h-2 bg-white rounded-full"></div>
                    )}
                  </div>
                </div>
              </td>
              <td className="px-4 py-3 text-center border-b">
                <div className="relative">
                  <input
                    type="radio"
                    id={`false-${questionId}-${statement.id}`}
                    name={`statement-${questionId}-${statement.id}`}
                    checked={answers[statement.id] === "false"}
                    onChange={() => handleAnswerSelect(statement.id, "false")}
                    className="sr-only peer"
                  />
                  <div
                    className="w-5 h-5 mx-auto rounded-full border-2 border-blue-500 peer-checked:bg-blue-500 peer-checked:border-blue-600 transition-colors flex items-center justify-center cursor-pointer"
                    onClick={() => handleAnswerSelect(statement.id, "false")}
                  >
                    {answers[statement.id] === "false" && (
                      <div className="w-2 h-2 bg-white rounded-full"></div>
                    )}
                  </div>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default TrueFalseTableQuestion;
