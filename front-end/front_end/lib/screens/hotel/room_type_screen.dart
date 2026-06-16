class RoomType {
  final int id;
  final String namaTipe;
  final String? foto;       // ✅ Tambahkan ini
  final double hargaAsli;
  final double hargaAkhir;
  final String? promoAktif;
  final int kapasitas;
  final String fasilitas;
  final String deskripsi;

  RoomType({
    required this.id,
    required this.namaTipe,
    this.foto,              // ✅ Tambahkan ini
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
      foto: json['foto'],   // ✅ Tambahkan ini
      hargaAsli: double.parse(json['harga_asli'].toString()),
      hargaAkhir: double.parse(json['harga_akhir'].toString()),
      promoAktif: json['promo_aktif'],
      kapasitas: json['kapasitas'],
      fasilitas: json['fasilitas'],
      deskripsi: json['deskripsi'],
    );
  }
}