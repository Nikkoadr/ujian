"use client";
import React, { useState } from "react";
import { Clock, Info, Grid3X3 } from "lucide-react";
import Header from "@/components/Header";
import QuestionContent from "@/components/QuestionContent";
import QuestionNavigation from "@/components/QuestionNavigation";
import QuestionListModal from "@/components/QuestionListModal";
import InfoModal from "@/components/InfoModal";
import Timer from "@/components/Timer";
import FinishModal from "@/components/FinishModal";
import { Answer } from "@/types";
import { questionData } from "@/data/questions";
import Swal from "sweetalert2";

function App() {
  const [currentQuestion, setCurrentQuestion] = useState<number>(1);
  const [answers, setAnswers] = useState<Record<number, Answer>>({});
  const [isUncertain, setIsUncertain] = useState<Record<number, boolean>>({});
  const [showQuestionList, setShowQuestionList] = useState(false);
  const [showInfo, setShowInfo] = useState(false);
  const [showFinishModal, setShowFinishModal] = useState(false);
  const [fontSize, setFontSize] = useState(16);
  const [timeLeft, setTimeLeft] = useState(794); // 13 menit 14 detik

  const totalQuestions = Object.keys(questionData).length;

  // handle jawaban
  const handleAnswerChange = (questionId: number, answer: Answer) => {
    setAnswers((prev) => ({ ...prev, [questionId]: answer }));
  };

  const handleUncertainChange = (questionId: number, uncertain: boolean) => {
    setIsUncertain((prev) => ({ ...prev, [questionId]: uncertain }));
  };

  const navigateToQuestion = (questionId: number) => {
    if (questionId >= 1 && questionId <= totalQuestions) {
      setCurrentQuestion(questionId);
      window.scrollTo(0, 0);
    }
  };

  const goToPrevious = () => {
    if (currentQuestion > 1) navigateToQuestion(currentQuestion - 1);
  };

  const goToNext = () => {
    if (currentQuestion < totalQuestions) {
      navigateToQuestion(currentQuestion + 1);
    } else {
      setShowFinishModal(true);
    }
  };

  const getQuestionStatus = (questionId: number) => {
    if (isUncertain[questionId]) return "uncertain";
    if (answers[questionId]) return "answered";
    return "unanswered";
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <Header />

      {/* Konten utama */}
      <div className="container mx-auto px-4 pt-24 pb-8 max-w-10xl">
        <div className="bg-white rounded-lg shadow-lg overflow-hidden">
          {/* Header Soal */}
          <div
            className="bg-gradient-to-r from-blue-700 to-blue-600 px-6 py-4 relative"
            style={{
              backgroundImage:
                "url('https://pusmendik.kemendikdasmen.go.id/tka/images/logo-w.png')",
              backgroundSize: "200px",
              backgroundRepeat: "no-repeat",
              backgroundPosition: "top right",
            }}
          >
            <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
              <div className="flex items-center gap-4">
                <span className="text-lg font-medium">Soal nomor</span>
                <span className="text-2xl font-bold">{currentQuestion}</span>
              </div>

              <div className="flex flex-wrap items-center gap-3">
                <button
                  onClick={() => setShowInfo(true)}
                  className="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-full text-sm font-medium transition-colors"
                >
                  <Info className="w-4 h-4" />
                  <span className="hidden sm:inline">INFORMASI SOAL</span>
                </button>

                <div className="flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full text-sm">
                  <Clock className="w-4 h-4" />
                  <span className="font-medium">Sisa Waktu:</span>
                  <Timer initialTime={timeLeft} />
                </div>

                <button
                  onClick={() => setShowQuestionList(true)}
                  className="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-full text-sm font-medium transition-colors"
                >
                  <span className="hidden sm:inline">Daftar Soal</span>
                  <Grid3X3 className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* Ukuran font & mapel */}
            <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center mt-4 pt-4 border-t border-blue-500/30">
              <div className="flex items-center gap-4">
                <span className="text-sm">Ukuran font soal:</span>
                <div className="flex gap-2">
                  {[12, 16, 20].map((size) => (
                    <button
                      key={size}
                      onClick={() => setFontSize(size)}
                      className={`px-2 py-1 rounded text-sm transition-colors ${
                        fontSize === size
                          ? "bg-white/20 font-bold"
                          : "hover:bg-white/10"
                      }`}
                      style={{ fontSize: `${size}px` }}
                    >
                      A
                    </button>
                  ))}
                </div>
              </div>

              <div className="text-right mt-2 lg:mt-0">
                <div className="text-lg font-semibold">Matematika</div>
              </div>
            </div>
          </div>

          {/* Konten Soal */}
          <div className="p-6">
            <QuestionContent
              question={questionData[currentQuestion]}
              questionId={currentQuestion}
              answer={answers[currentQuestion]}
              onAnswerChange={handleAnswerChange}
              fontSize={fontSize}
            />
          </div>

          {/* Navigasi */}
          <div className="border-t border-gray-200 p-6">
            <QuestionNavigation
              currentQuestion={currentQuestion}
              totalQuestions={totalQuestions}
              isUncertain={isUncertain[currentQuestion] || false}
              onPrevious={goToPrevious}
              onNext={goToNext}
              onUncertainChange={(uncertain) =>
                handleUncertainChange(currentQuestion, uncertain)
              }
            />
          </div>
        </div>
      </div>

      {/* Modals */}
      <QuestionListModal
        isOpen={showQuestionList}
        onClose={() => setShowQuestionList(false)}
        totalQuestions={totalQuestions}
        currentQuestion={currentQuestion}
        answers={answers}
        isUncertain={isUncertain}
        onQuestionSelect={navigateToQuestion}
        getQuestionStatus={getQuestionStatus}
      />

      <InfoModal isOpen={showInfo} onClose={() => setShowInfo(false)} />

      <FinishModal
        isOpen={showFinishModal}
        onClose={() => setShowFinishModal(false)}
        totalQuestions={totalQuestions}
        answeredCount={Object.keys(answers).length}
        doubtfulCount={Object.values(isUncertain).filter(Boolean).length}
        timeLeft={timeLeft}
        onFinish={() => {
          Swal.fire({
            title: "Ujian Selesai!",
            text: "Hasil akan diproses.",
            icon: "success",
            timer: 2000, // otomatis hilang setelah 2 detik
            showConfirmButton: false,
            position: "top-end", // muncul di pojok kanan atas
            toast: true, // tampil sebagai toast kecil
            timerProgressBar: true,
          }).then(() => {
            window.location.href = "/logout";
          });
        }}
      />
    </div>
  );
}

export default App;
