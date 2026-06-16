// models/room_type.dart

class RoomType {
  final int id;
  final String namaTipe;
  final String? foto;
  final double hargaAsli;
  final double hargaAkhir;
  final dynamic promoAktif;
  final int kapasitas;
  final String fasilitas;
  final String deskripsi;

  RoomType({
    required this.id,
    required this.namaTipe,
    this.foto,
    required this.hargaAsli,
    required this.hargaAkhir,
    this.promoAktif,
    required this.kapasitas,
    required this.fasilitas,
    required this.deskripsi,
  });

  factory RoomType.fromJson(Map<String, dynamic> json) {
    return RoomType(
      id: json['id'] ?? 0,
      namaTipe: json['nama_tipe'] ?? json['name'] ?? 'Kamar',
      foto: json['foto'] ?? json['image'] ?? '',
      hargaAsli: (json['harga_asli'] as num?)?.toDouble() ?? 0.0,
      hargaAkhir: (json['harga_akhir'] as num?)?.toDouble() ?? 0.0,
      promoAktif: json['promo_aktif'],
      kapasitas: (json['kapasitas'] as num?)?.toInt() ?? 0,
      fasilitas: json['fasilitas'] ?? '',
      deskripsi: json['deskripsi'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama_tipe': namaTipe,
      'foto': foto,
      'harga_asli': hargaAsli,
      'harga_akhir': hargaAkhir,
      'promo_aktif': promoAktif,
      'kapasitas': kapasitas,
      'fasilitas': fasilitas,
      'deskripsi': deskripsi,
    };
  }
}