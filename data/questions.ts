import { QuestionData } from "@/types";

export const questionData: QuestionData = {
  1: {
    id: 1,
    type: "multiple_choice",
    content: "<p>&nbsp;</p>",
    question:
      '<p><img src="/hero1.webp" alt="\\frac {1} {4}+\\, \\frac {7} {4}\\, \\times \\, \\frac {8} {21}\\, =\\, ...." /></p>',
    options: [
      {
        id: "a",
        text: "",
        image: "/hero1.webp",
      },
      {
        id: "b",
        text: "",
        image: "/hero1.webp",
      },
      {
        id: "c",
        text: "",
        image: "/hero1.webp",
      },
      {
        id: "d",
        text: "",
        image: "/hero1.webp",
      },
      {
        id: "e",
        text: "",
        image: "/hero1.webp",
      },
    ],
  },
  2: {
    id: 2,
    type: "true_false_table",
    content:
      "<p><span style=\"font-family: 'times new roman', times; font-size: 14px;\">Mirna akan memproduksi dua jenis kue dengan modal Rp8.000.000,00. Biaya produksi kue bolu sebesar Rp15.000,00 per kotak dan dijual dengan laba 40%. Sedangkan biaya produksi kue brownies sebesar Rp20.000,00 per kotak dan dijual dengan laba 35%. Setiap harinya, Mirna dapat memproduksi paling banyak 500 kotak kue.</span></p>",
    question:
      "<p><span style=\"font-family: 'times new roman', times; font-size: 14px;\">Apabila Mirna ingin memperoleh keuntungan maksimum, tentukan Benar atau Salah untuk setiap pernyataan berikut!</span></p>",
    statements: [
      {
        id: "A",
        text: "<p><span style=\"font-family: 'times new roman', times, serif; font-size: 14px;\">Mirna harus memproduksi 200 kotak kue bolu.</span></p>",
      },
      {
        id: "B",
        text: "<p><span style=\"font-family: 'times new roman', times, serif; font-size: 14px;\">Mirna harus memproduksi kue brownies lebih banyak.</span></p>",
      },
      {
        id: "C",
        text: "<p><span style=\"font-family: 'times new roman', times, serif; font-size: 14px;\">Keuntungan maksimum yang dapat diperoleh Mirna adalah Rp3.100.000,00.</span></p>",
      },
    ],
  },
  3: {
    id: 3,
    type: "true_false_table",
    content: '<p><img src="/hero1.webp" alt="" width="538" height="214"></p>',
    question:
      "<p><span style=\"font-family: 'times new roman', times; font-size: 16px;\">Tentukan Benar atau Salah untuk setiap pernyataan berikut terkait dengan besar sudut pada trapesium ABCD!</span></p>",
    statements: [
      {
        id: "A",
        text: "",
        image: "/hero1.webp",
      },
      {
        id: "B",
        text: "",
        image: "/hero1.webp",
      },
      {
        id: "C",
        text: "",
        image: "/hero1.webp",
      },
    ],
  },
  4: {
    id: 4,
    type: "multiple_choice",
    content: '<p><img src="/hero1.webp" alt="" width="453" height="129"></p>',
    question:
      "<p><span style=\"font-family: 'times new roman', times; font-size: 16px;\">Putuskan apakah dengan tambahan informasi Pernyataan (1) dan Pernyataan (2) berikut cukup untuk menjawab pertanyaan tersebut!</span><br><span style=\"font-family: 'times new roman', times; font-size: 16px;\">(1) Luas trapesium ABCD = 24.</span><br><span style=\"font-family: 'times new roman', times; font-size: 16px;\">(2) BC = 10 dan CD = 5.</span></p>",
    options: [
      {
        id: "a",
        text: "<p><span style=\"font-family: 'times new roman', times, serif;\">Pernyataan (1) SAJA cukup untuk menjawab pertanyaan, tetapi Pernyataan (2) SAJA tidak cukup.</span></p>",
      },
      {
        id: "b",
        text: "<p><span style=\"font-family: 'times new roman', times, serif;\">Pernyataan (2) SAJA cukup untuk menjawab pertanyaan, tetapi Pernyataan (1) SAJA tidak cukup.</span></p>",
      },
      {
        id: "c",
        text: "<p><span style=\"font-family: 'times new roman', times, serif;\">DUA pernyataan BERSAMA-SAMA cukup untuk menjawab pertanyaan, tetapi SATU pernyataan SAJA tidak cukup.</span></p>",
      },
      {
        id: "d",
        text: "<p><span style=\"font-family: 'times new roman', times, serif;\">Pernyataan (1) SAJA cukup untuk menjawab pertanyaan dan Pernyataan (2) SAJA cukup.</span></p>",
      },
      {
        id: "e",
        text: "<p><span style=\"font-family: 'times new roman', times, serif; font-size: 16px;\">Pernyataan (1) dan Pernyataan (2) tidak cukup untuk menjawab pertanyaan.</span></p>",
      },
    ],
  },
  5: {
    id: 5,
    type: "multiple_choice",
    content:
      '<p>Suatu tangga dengan panjang 6 meter disandarkan pada dinding vertikal. Sudut yang dibentuk tangga dengan lantai adalah 60°.</p><p><img src="/soal_gambar/62066_59d3ef955657eaf8f5a160bbd6c2b198.png" alt=""></p>',
    question:
      "<p>Tinggi dinding yang disentuh ujung atas tangga adalah ....</p>",
    options: [
      { id: "a", text: "<p>3 meter</p>" },
      { id: "b", text: "<p>3√2 meter</p>" },
      { id: "c", text: "<p>3√3 meter</p>" },
      { id: "d", text: "<p>4√2 meter</p>" },
      { id: "e", text: "<p>4√3 meter</p>" },
    ],
  },
  6: {
    id: 6,
    type: "multiple_select",
    content:
      "<p><span style=\"font-family: 'times new roman', times; font-size: 16px;\">Rata-rata nilai ulangan 17 murid dari skala 100 adalah 83. Ada 3 murid yang mengikuti ujian susulan sehingga rata-rata nilai ulangan dari 20 murid menjadi 82.</span></p>",
    question:
      "<p><span style=\"font-family: 'times new roman', times; font-size: 16px;\">Tentukan semua pernyataan berikut yang benar terkait dengan nilai ketiga murid yang mengikuti ujian susulan! Jawaban benar lebih dari satu.</span></p>",
    options: [
      {
        id: "a",
        text: "<p><span style=\"font-family: 'times new roman', times, serif; font-size: 16px;\">Jumlah nilai ketiga murid yang mengikuti ujian susulan adalah 229.</span></p>",
      },
      {
        id: "b",
        text: "<p><span style=\"font-family: 'times new roman', times, serif; font-size: 16px;\">Rata-rata nilai ketiga murid yang mengikuti ujian susulan lebih dari 70.</span></p>",
      },
      {
        id: "c",
        text: "<p><span style=\"font-family: 'times new roman', times, serif; font-size: 16px;\">Nilai terendah dari ketiga murid yang mengikuti ujian susulan tidak kurang dari 29.</span></p>",
      },
      {
        id: "d",
        text: "<p><span style=\"font-family: 'times new roman', times, serif; font-size: 16px;\">Nilai tertinggi dari ketiga murid yang mengikuti ujian susulan lebih dari 76.</span></p>",
      },
      {
        id: "e",
        text: "<p><span style=\"font-family: 'times new roman', times, serif; font-size: 16px;\">Jangkauan data nilai ketiga murid yang mengikuti ujian susulan lebih dari 72.</span></p>",
      },
    ],
  },
  7: {
    id: 7,
    type: "true_false_table",
    content:
      '<p>Fungsi didefinisikan oleh <img src="/hero1.webp" alt="f(x) = 4(x²-8x+12)"></p>',
    question:
      "<p>Tentukan <strong>Benar</strong> atau <strong>Salah</strong> pada setiap pernyataan berikut yang terkait dengan grafik fungsi f!</p>",
    statements: [
      { id: "A", text: "<p>Grafik fungsi f terbuka ke atas.</p>" },
      { id: "B", text: "<p>Grafik fungsi f memotong garis y = -18</p>" },
      { id: "C", text: "<p>Grafik fungsi f tidak melalui kuadran tiga.</p>" },
    ],
  },
  8: {
    id: 8,
    type: "multiple_select",
    content:
      '<p>Tagihan listrik bulanan di sebuah apartemen dihitung berdasarkan jumlah energi listrik (dalam kWh) yang digunakan. Apartemen tersebut masih menggunakan meteran listrik yang berbeda dari penggunaan token.</p><p>Biaya tagihan listrik dihitung dengan rumus:</p><p style="text-align: center;"><img src="/hero1.webp" alt="f(x) = 1.350x + 25.000"></p><p>Keterangan: f(x): total tagihan listrik (dalam rupiah), x: jumlah pemakaian energi listrik (kwh)</p><p>Andi merupakan salah satu penghuni apartemen tersebut yang menerima tagihan pembayaran listrik seperti terlihat pada gambar berikut:</p><p style="text-align: center;"><img src="/hero1.webp" alt="" width="714" height="362"></p><p>Dia menyadari bahwa penggunaan listrik sebulan terakhir lebih dari penggunaan listrik biasanya.</p>',
    question:
      "<p>Berdasarkan informasi tersebut, biasanya berapa besar penggunaan listrik di apartemen Andi?</p><p>Pilihlah jawaban yang benar! Jawaban benar lebih dari satu.</p>",
    options: [
      { id: "a", text: "<p>85 kWh</p>" },
      { id: "b", text: "<p>90 kWh</p>" },
      { id: "c", text: "<p>100 kWh</p>" },
      { id: "d", text: "<p>120 kWh</p>" },
      { id: "e", text: "<p>137 kWh</p>" },
    ],
  },
  9: {
    id: 9,
    type: "true_false_table",
    content:
      '<p>Pak Andi akan mempresentasikan desain gedung berukuran 60 cm × 60 cm menggunakan proyektor ke layar berukuran 2,4 meter × 1,8 meter yang dipasang di depan ruang rapat (orientasi horizontal). Proyektor menghasilkan pembesaran proporsional tergantung jaraknya dari layar.</p><p>Pak Andi menempatkan proyektor dengan jarak yang menghasilkan skala pembesaran seperti terlihat pada gambar berikut:</p><p style="text-align: center;"><img src="/hero1.webp" alt="" width="440" height="442"></p>',
    question:
      "<p>Bagaimanakah tampilan desain gedung di layar?</p><p>Tentukan <strong>Benar</strong> atau <strong>Salah</strong> pada setiap pernyataan berikut!</p>",
    statements: [
      {
        id: "A",
        text: "<p>Perbandingan ukuran tampilan desain di layar adalah 1 : 1.</p>",
      },
      {
        id: "B",
        text: "<p>Ukuran panjang dan lebar tampilan desain pada layar adalah lebih dari 1 meter.</p>",
      },
      {
        id: "C",
        text: "<p>Terdapat bagian gambar asli desain yang terpotong dalam tampilan pada layar.</p>",
      },
    ],
  },
  10: {
    id: 10,
    type: "multiple_choice",
    content:
      '<p>Luki adalah panitia bazar di sekolahnya. Dia mendapat tugas dari ketua pelaksana untuk membuat kupon. Dia ingin di setiap kupon memiliki kode akses yang unik. Kode akses kupon bazar itu memiliki lima karakter dengan format sebagai berikut:</p><table width="499"><tbody><tr><td width="106"><p style="text-align: right;">AXBYC</p></td></tr></tbody></table>',
    question:
      "<p>dengan A, B, dan C menyatakan huruf, serta X dan Y menyatakan angka. Tidak boleh ada angka dan huruf yang diulang. Berapakah berapa banyak kode akses berbeda yang dapat dibuat?</p>",
    options: [
      { id: "a", text: "<p>1.263.600</p>" },
      { id: "b", text: "<p>1.352.000</p>" },
      { id: "c", text: "<p>1.404.000</p>" },
      { id: "d", text: "<p>1.423.656</p>" },
      { id: "e", text: "<p>1.757.600</p>" },
    ],
  },
};
