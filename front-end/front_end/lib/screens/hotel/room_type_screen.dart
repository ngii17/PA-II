class RoomType {
  final int id;
  final String namaTipe;
  final double hargaAsli;   // Ganti dari harga ke hargaAsli
  final double hargaAkhir;  // Tambahan untuk harga diskon
  final String? promoAktif; // Tambahan untuk teks promo (bisa kosong/null)
  final int kapasitas;
  final String fasilitas;
  final String deskripsi;

  RoomType({
    required this.id,
    required this.namaTipe,
    required this.hargaAsli,
    required this.hargaAkhir,
    this.promoAktif,
    required this.kapasitas,
    required this.fasilitas,
    required this.deskripsi,
  });

  factory RoomType.fromJson(Map<String, dynamic> json) {
    return RoomType(
      id: json['id'],
      namaTipe: json['nama_tipe'],
      // Ambil data baru dari JSON Laravel
      hargaAsli: double.parse(json['harga_asli'].toString()),
      hargaAkhir: double.parse(json['harga_akhir'].toString()),
      promoAktif: json['promo_aktif'], // Bisa berisi "Diskon 10%" atau null
      kapasitas: json['kapasitas'],
      fasilitas: json['fasilitas'],
      deskripsi: json['deskripsi'],
    );
  }
}