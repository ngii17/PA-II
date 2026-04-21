class MenuResto {
  final int id;
  final String namaMenu;
  final String deskripsi;
  final double harga;
  final String? fotoMenu;
  final String kategori; // Diambil dari relasi kategori -> nama_kategori
  final String status;   // Diambil dari relasi status -> nama_status

  MenuResto({
    required this.id,
    required this.namaMenu,
    required this.deskripsi,
    required this.harga,
    this.fotoMenu,
    required this.kategori,
    required this.status,
  });

  // Fungsi untuk mengubah JSON Laravel (beserta relasinya) menjadi Object Flutter
  factory MenuResto.fromJson(Map<String, dynamic> json) {
    return MenuResto(
      id: json['id'],
      namaMenu: json['nama_menu'],
      deskripsi: json['deskripsi'],
      // Pastikan harga dikonversi ke double dengan aman
      harga: double.parse(json['harga'].toString()),
      fotoMenu: json['foto_menu'],
      // Kita ambil nama_kategori dari objek 'kategori' yang dikirim Laravel
      kategori: json['kategori']['nama_kategori'] ?? "Umum",
      // Kita ambil nama_status dari objek 'status' yang dikirim Laravel
      status: json['status']['nama_status'] ?? "Tersedia",
    );
  }
}