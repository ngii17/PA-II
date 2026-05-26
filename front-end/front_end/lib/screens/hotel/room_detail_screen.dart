import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:vibration/vibration.dart';

// Services & Model
import '../../services/api_services.dart';
import 'room_type_screen.dart'; 
import 'booking_screen.dart';

// Screens & Widgets
import '../event/event_header.dart'; 
import '../user/login_screen.dart';
import '../user/register_screen.dart';
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class RoomDetailScreen extends StatefulWidget {
  final RoomType room;
  const RoomDetailScreen({super.key, required this.room});

  @override
  State<RoomDetailScreen> createState() => _RoomDetailScreenState();
}

class _RoomDetailScreenState extends State<RoomDetailScreen> with SingleTickerProviderStateMixin {
  late Future<Map<String, dynamic>> _reviewData;
  
  late AnimationController _shakeController;
  late Animation<double> _shakeAnimation;

  @override
  void initState() {
    super.initState();
    _refreshReviews();

    _shakeController = AnimationController(
      duration: const Duration(milliseconds: 500),
      vsync: this,
    );

    _shakeAnimation = TweenSequence<double>([
      TweenSequenceItem(tween: Tween(begin: 0.0, end: 12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: 12.0, end: -12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: -12.0, end: 12.0), weight: 1),
      TweenSequenceItem(tween: Tween(begin: 12.0, end: 0.0), weight: 1),
    ]).animate(CurvedAnimation(parent: _shakeController, curve: Curves.easeInOut));
  }

  @override
  void dispose() {
    _shakeController.dispose();
    super.dispose();
  }

  void _refreshReviews() {
    setState(() {
      _reviewData = ApiServices.getHotelReviews(widget.room.id);
    });
  }

  Future<void> _handleBookingAction(BuildContext context) async {
    final SharedPreferences prefs = await SharedPreferences.getInstance();
    String? token = prefs.getString('auth_token');
    bool hasReg = prefs.getBool('has_registered') ?? false;

    if (token != null) {
      if (!mounted) return;
      Navigator.push(context, MaterialPageRoute(builder: (context) => BookingScreen(room: widget.room)));
    } else {
      Vibration.hasVibrator().then((has) { if (has == true) Vibration.vibrate(duration: 100); });
      _shakeController.forward(from: 0.0);
      
      if (!mounted) return;
      _showGuestCautionDialog(context, hasReg);
    }
  }

  void _showGuestCautionDialog(BuildContext context, bool hasRegistered) {
    showDialog(
      context: context,
      builder: (c) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
        title: const Row(
          children: [
            Icon(Icons.lock_person_rounded, color: AppTheme.goldAccent),
            SizedBox(width: 10),
            Text("Butuh Akun", style: TextStyle(fontWeight: FontWeight.bold)),
          ],
        ),
        content: const Text(
          "Untuk melakukan reservasi, Anda perlu masuk ke akun terlebih dahulu agar data inap Anda tercatat dengan aman.",
          style: TextStyle(color: Colors.black54, fontSize: 13, height: 1.5),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(c),
            child: const Text("Nanti Saja", style: TextStyle(color: Colors.grey)),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.primaryBlue,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            ),
            onPressed: () {
              Navigator.pop(c);
              Navigator.push(
                context, 
                MaterialPageRoute(builder: (c) => hasRegistered ? const LoginScreen() : const RegisterScreen())
              );
            },
            child: const Text("Masuk", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      extendBodyBehindAppBar: true, 
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: Padding(
          padding: const EdgeInsets.all(8.0),
          child: CircleAvatar(
            backgroundColor: Colors.black.withOpacity(0.35),
            child: IconButton(
              icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 18),
              onPressed: () => Navigator.pop(context),
            ),
          ),
        ),
      ),
      body: AnimatedBuilder(
        animation: _shakeAnimation,
        builder: (context, child) => Transform.translate(offset: Offset(_shakeAnimation.value, 0), child: child),
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Stack(
                children: [
                  ClipRRect(
                    borderRadius: const BorderRadius.only(
                      bottomLeft: Radius.circular(60),
                      bottomRight: Radius.circular(60),
                    ),
                    child: Image.network(
                      widget.room.fotoTipe ?? "https://images.unsplash.com/photo-1566073771259-6a8506099945",
                      height: 420,
                      width: double.infinity,
                      fit: BoxFit.cover,
                      errorBuilder: (c,e,s) => Container(height: 420, color: Colors.grey[200], child: const Icon(Icons.broken_image, size: 50)),
                    ),
                  ),
                  Positioned.fill(
                    child: Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                          colors: [Colors.black.withOpacity(0.1), Colors.transparent, Colors.black.withOpacity(0.7)],
                        ),
                        borderRadius: const BorderRadius.only(bottomLeft: Radius.circular(60), bottomRight: Radius.circular(60)),
                      ),
                    ),
                  ),
                  Positioned(
                    bottom: 45, left: 30, right: 30,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(widget.room.namaTipe, 
                          style: const TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.w900, letterSpacing: 0.5)),
                        const SizedBox(height: 8),
                        const Row(
                          children: [
                            Icon(Icons.location_on_rounded, color: AppTheme.goldAccent, size: 18),
                            SizedBox(width: 8),
                            Text("Purnama Balige Hotel, Toba", style: TextStyle(color: Colors.white70, fontSize: 14, fontWeight: FontWeight.w500)),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),

              const EventHeader(),

              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text("Harga per Malam", style: TextStyle(color: Colors.grey, fontSize: 11, fontWeight: FontWeight.bold, letterSpacing: 1)),
                            const SizedBox(height: 6),
                            Text("Rp ${widget.room.hargaAkhir.toStringAsFixed(0)}", 
                              style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: primaryColor)),
                          ],
                        ),
                        if (widget.room.promoAktif != null)
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                            decoration: BoxDecoration(
                              color: const Color(0xFFFFF7E6), 
                              borderRadius: BorderRadius.circular(15), 
                              border: Border.all(color: AppTheme.goldAccent.withOpacity(0.3))
                            ),
                            child: Text(widget.room.promoAktif!, 
                              style: const TextStyle(color: AppTheme.goldAccent, fontWeight: FontWeight.w900, fontSize: 12)),
                          ),
                      ],
                    ),

                    const SizedBox(height: 35),
                    const Text("Fasilitas Unggulan", style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Colors.black87)),
                    const SizedBox(height: 18),
                    Wrap(
                      spacing: 12, runSpacing: 12,
                      children: widget.room.fasilitas.split(',').map((f) => _buildFacilityChip(f.trim())).toList(),
                    ),

                    const SizedBox(height: 40),
                    const Text("Tentang Kamar", style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Colors.black87)),
                    const SizedBox(height: 15),
                    Text(widget.room.deskripsi, 
                      textAlign: TextAlign.justify, 
                      style: TextStyle(color: Colors.grey.shade700, height: 1.7, fontSize: 15, fontWeight: FontWeight.w500)),

                    const SizedBox(height: 45),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text("Ulasan Tamu", style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Colors.black87)),
                        IconButton(onPressed: _refreshReviews, icon: Icon(Icons.sync_rounded, color: primaryColor))
                      ],
                    ),
                    
                    FutureBuilder<Map<String, dynamic>>(
                      future: _reviewData,
                      builder: (context, snapshot) {
                        if (snapshot.connectionState == ConnectionState.waiting) return const Center(child: CircularProgressIndicator());
                        List<dynamic> reviews = snapshot.data?['data'] ?? [];
                        if (reviews.isEmpty) return const Padding(padding: EdgeInsets.only(top: 10), child: Text("Belum ada ulasan untuk tipe kamar ini.", style: TextStyle(color: Colors.grey, fontStyle: FontStyle.italic)));
                        return Column(children: reviews.map((rev) => _buildReviewCard(rev)).toList());
                      },
                    ),
                    const SizedBox(height: 130), 
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.fromLTRB(25, 20, 25, 35),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.08), blurRadius: 25, offset: const Offset(0, -5))],
          borderRadius: const BorderRadius.vertical(top: Radius.circular(40)),
        ),
        child: SizedBox(
          width: double.infinity, height: 58,
          child: ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: primaryColor, 
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
              elevation: 8,
              shadowColor: primaryColor?.withOpacity(0.4),
            ),
            onPressed: () => _handleBookingAction(context), 
            child: const Text("PESAN SEKARANG", style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900, letterSpacing: 1.5)),
          ),
        ),
      ),
    );
  }

  Widget _buildFacilityChip(String label) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white, 
        borderRadius: BorderRadius.circular(18), 
        border: Border.all(color: Colors.grey.shade100),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 5)]
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Icon(Icons.check_circle_rounded, color: AppTheme.goldAccent, size: 18),
          const SizedBox(width: 10),
          Text(label, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: Colors.black87)),
        ],
      ),
    );
  }

  Widget _buildReviewCard(dynamic rev) {
    return Container(
      margin: const EdgeInsets.only(bottom: 18),
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: Colors.white, 
        borderRadius: BorderRadius.circular(25), 
        border: Border.all(color: Colors.grey.shade50),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 15, offset: const Offset(0, 5))]
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(children: List.generate(5, (i) => Icon(i < (rev['rating'] ?? 0) ? Icons.star_rounded : Icons.star_outline_rounded, color: AppTheme.goldAccent, size: 22))),
              const Text("Tamu Terverifikasi", style: TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.w900, letterSpacing: 1)),
            ],
          ),
          const SizedBox(height: 15),
          Text("\"${rev['komentar']}\"", 
            style: const TextStyle(fontSize: 14, color: Colors.black87, height: 1.6, fontStyle: FontStyle.italic, fontWeight: FontWeight.w500)),
        ],
      ),
    );
  }
}