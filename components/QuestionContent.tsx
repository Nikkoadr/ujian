import React from "react";
import { Question, Answer } from "@/types";
import MultipleChoiceQuestion from "./questions/MultipleChoiceQuestion";
import MultipleSelectQuestion from "./questions/MultipleSelectQuestion";
import TrueFalseTableQuestion from "./questions/TrueFalseTableQuestion";

interface QuestionContentProps {
  question: Question;
  questionId: number;
  answer?: Answer;
  onAnswerChange: (questionId: number, answer: Answer) => void;
  fontSize: number;
}

const QuestionContent: React.FC<QuestionContentProps> = ({
  question,
  questionId,
  answer,
  onAnswerChange,
  fontSize,
}) => {
  if (!question) {
    return (
      <div className="text-center py-8">
        <p className="text-gray-500">Soal tidak ditemukan</p>
      </div>
    );
  }

  const commonProps = {
    question,
    questionId,
    answer,
    onAnswerChange,
    fontSize,
  };

  return (
    <div className="border-2 border-gray-100 border-t-8 border-t-gray-200 rounded-lg">
      <div className="grid grid-cols-1 lg:grid-cols-2 min-h-[400px]">
        {/* Question Content */}
        <div className="p-6 pr-6 lg:pr-4 overflow-auto max-h-[500px] border-r-0 lg:border-r-4 lg:border-dotted lg:border-gray-200">
          <div
            className="prose prose-sm max-w-none"
            style={{ fontSize: `${fontSize}px` }}
            dangerouslySetInnerHTML={{ __html: question.content }}
          />
          {question.images &&
            question.images.map((img, index) => (
              <img
                key={index}
                src={img}
                alt=""
                className="max-w-full h-auto mt-4"
              />
            ))}
        </div>

        {/* Answer Options */}
        <div className="p-6 pl-6 lg:pl-4 overflow-auto max-h-[500px]">
          <div
            className="prose prose-sm max-w-none mb-4"
            style={{ fontSize: `${fontSize}px` }}
            dangerouslySetInnerHTML={{ __html: question.question }}
          />

          {question.type === "multiple_choice" && (
            <MultipleChoiceQuestion {...commonProps} />
          )}
          {question.type === "multiple_select" && (
            <MultipleSelectQuestion {...commonProps} />
          )}
          {question.type === "true_false_table" && (
            <TrueFalseTableQuestion {...commonProps} />
          )}
        </div>
      </div>
    </div>
  );
};

export default QuestionContent;
