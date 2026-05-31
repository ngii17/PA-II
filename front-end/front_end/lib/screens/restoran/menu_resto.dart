class MenuResto {
  final int id;
  final String namaMenu;
  final String deskripsi;
  final double hargaAsli;   // Diperbarui
  final double hargaAkhir;  // Diperbarui
  final String? promoAktif; // Diperbarui (Bisa null)
  final String? fotoMenu;
  final String kategori; 
  final String status;   

  MenuResto({
    required this.id,
    required this.namaMenu,
    required this.deskripsi,
    required this.hargaAsli,
    required this.hargaAkhir,
    this.promoAktif,
    this.fotoMenu,
    required this.kategori,
    required this.status,
  });

  factory MenuResto.fromJson(Map<String, dynamic> json) {
    return MenuResto(
      id: json['id'],
      namaMenu: json['nama_menu'],
      deskripsi: json['deskripsi'],
      // SINKRONISASI: Ambil kunci baru dari Laravel
      hargaAsli: double.parse(json['harga_asli'].toString()),
      hargaAkhir: double.parse(json['harga_akhir'].toString()),
      promoAktif: json['promo_aktif'], 
      fotoMenu: json['foto_menu'],
      kategori: json['kategori']['nama_kategori'] ?? "Umum",
      status: json['status']['nama_status'] ?? "Tersedia",
    );
  }
}