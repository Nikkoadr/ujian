export interface Answer {
  type: 'single' | 'multiple' | 'complex';
  value: string | string[] | Record<string, string>;
}

export interface Question {
  id: number;
  type: 'multiple_choice' | 'multiple_select' | 'true_false_table';
  content: string;
  question: string;
  options?: Array<{
    id: string;
    text: string;
    image?: string;
  }>;
  statements?: Array<{
    id: string;
    text: string;
    image?: string;
  }>;
  images?: string[];
}

export type QuestionData = Record<number, Question>;