// models/menu_resto.dart

class MenuResto {
  final int id;
  final String namaMenu;
  final String deskripsi;
  final double hargaAsli;
  final double hargaAkhir;
  final dynamic promoAktif;
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
    // Handle kategori yang bisa berupa string atau object
    String kategoriValue = 'Umum';
    if (json['kategori'] != null) {
      if (json['kategori'] is Map) {
        kategoriValue = json['kategori']['nama_kategori'] ?? 'Umum';
      } else if (json['kategori'] is String) {
        kategoriValue = json['kategori'];
      }
    }

    // Handle status yang bisa berupa string atau object
    String statusValue = 'Tersedia';
    if (json['status'] != null) {
      if (json['status'] is Map) {
        statusValue = json['status']['nama_status'] ?? 'Tersedia';
      } else if (json['status'] is String) {
        statusValue = json['status'];
      }
    }

    return MenuResto(
      id: json['id'] ?? 0,
      namaMenu: json['nama_menu'] ?? 'Menu',
      deskripsi: json['deskripsi'] ?? '',
      hargaAsli: (json['harga_asli'] as num?)?.toDouble() ?? 0.0,
      hargaAkhir: (json['harga_akhir'] as num?)?.toDouble() ?? 0.0,
      promoAktif: json['promo_aktif'],
      fotoMenu: json['foto_menu'] ?? json['foto'] ?? '',
      kategori: kategoriValue,
      status: statusValue,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama_menu': namaMenu,
      'deskripsi': deskripsi,
      'harga_asli': hargaAsli,
      'harga_akhir': hargaAkhir,
      'promo_aktif': promoAktif,
      'foto_menu': fotoMenu,
      'kategori': kategori,
      'status': status,
    };
  }
}