# pertemuan-01
a) Kesinambungan PWD–DPW–DPWL 
Jawaban:DPWL merupakan kelanjutan dari PWD dan DPW.
Pada PWD yg di pelajari mahasiswa pengenalan bahasa yang digunakan untuk membuat web seperti **HTML**, **CSS**, **JavaScript**, **PHP**.
Kemudian pada DPW, kemampuan tersebut dikembangkan menjadi PHP yang lebih terstruktur, seperti modularisasi, MySQLi, prepared statement, autentikasi, session, CRUD, transaksi. Untuk DPWL tidak dimulai dari awal, tetapi mengorganisasikan kembali kompetensi web sebelumnya ke dalam arsitektur MVC (*Model*, *View*, *Controller*).


b) Perbedaan PHP terstruktur dan MVC
Jawaban:
Perbedaan utamanya adalah pada cara membagi dan mengorganisasikan tanggung jawab dalam aplikasi.

Pada PHP terstruktur, dalam satu file atau halaman bisa terdapat beberapa bagian sekaligus, misalnya:
menerima request dari pengguna, menjalankan proses, melakukan query ke *database* yg di buat, dan menampilkan HTML.

Sedangkan pada MVC, tanggung jawab tersebut dipisahkan menjadi tiga bagian:
*Model* :mengelola data dan database.
*View* : menampilkan informasi kepada pengguna nya
*Controller* : menerima request dan mengatur jalannya proses.
Contoh seperti: Pada PHP terstruktur, proses login, query database, dan tampilan halaman login bisa berada dalam satu file.

Pada MVC, proses tersebut dipisahkan agar aplikasi lebih terorganisasi dan pemisahan bagian-bagian lebih jelas.


c) Fungsi Model, View, dan Controller
Jawaban:
1. Model
Fungsi: mengelola data dan berhubungan dengan database. Untuk batasnya, Model bukan tempat untuk membuat tampilan HTML.

2. View
Fungsi: menampilkan informasi kepada pengguna dan menyediakan tampilan/interaksi. Sehingga batasan nya, View bukan tempat utama untuk query database atau logika bisnis.

3. Controller
Fungsi: menerima request dan mengatur alur proses aplikasi. Dan untuk batasan nya, Controller bukan tempat untuk membuat tampilan HTML yang panjang atau mencampurkan query database secara langsung.


d) Alur request–response MVC
Jawaban: 
1. Pengguna > Browser
Pengguna melakukan suatu tindakan, misalnya membuka halaman atau mengirim form login.

2. Browser > Controller
Browser mengirimkan request kepada aplikasi. Controller menerima request dan menentukan proses yang harus dilakukan selanjutnya.

3. Controller > Model
Jika membutuhkan data, Controller bisa meminta Model untuk mengambil atau mengolah data.

4. Model > Database
Model berinteraksi dengan database untuk mengambil atau mengubah data yang ada.

5. Database > Model
Database memberikan hasil kepada Model.

6. Model > Controller
Model mengembalikan hasil pengolahan atau data kepada Controller.

7. Controller > View
Controller menentukan data yang akan diberikan kepada View.

8. View > Browser > Pengguna
View menyusun tampilan atau response, kemudian hasilnya dikirim kembali melalui browser kepada pengguna tersebut.


e)Pemetaan satu atau beberapa bagian/fitur aplikasi DPW ke Model, Controller, dan View disertai alasan
Jawab: Misalnya pada aplikasi DPW terdapat fitur login admin.
Model: Model	Query untuk mencari data username/password admin di database.
Alasannya: Karena Model bertugas mengakses dan mengelola data.

Controller : Menerima data login, mengatur proses login.
Alasannya : Karena Controller menentukan data yang akan di teruskan.

View : Halaman/form login dan pesan seperti “Login berhasil” atau “Username/password salah”
Alasannya : Karena View bertugas menampilkan informasi kepada pengguna

jadi Query dan akses database berada di Model, proses login berada di Controller, sedangkan form dan hasil yang dilihat pengguna itu berada di View.


f) Kesimpulan P1
Jawab: Modul P1 menjelaskan bahwa DPWL merupakan lanjutan dari PWD dan DPW dengan tujuan membuat aplikasi web yang lebih terstruktur menggunakan konsep MVC (Model mengelola data, Controller mengatur proses, dan View menampilkan informasi kepada pengguna).

Melalui MVC juga mahasiswa itu belajar memisahkan tanggung jawab setiap bagian aplikasi, sehingga pengembangan menjadi lebih terorganisasi. Pada materi P1, fokusnya pada memahami konsep dan memetakan aplikasi DPW ke MVC, serta README.md pada root repositori berfungsi sebagai halaman utama yang menjelaskan identitas 
proyek pembelajaran dan perkembangan. GitHub juga sebagai repositori daring untuk menyimpan, mengelola.