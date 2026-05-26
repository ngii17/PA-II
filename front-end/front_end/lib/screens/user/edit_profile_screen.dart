import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:shared_preferences/shared_preferences.dart';

// Services & Widgets
import '../../services/api_services.dart';
import '../event/event_header.dart'; 
import '../../colors/login_constants.dart';
import '../../widgets/login_widgets.dart';

class EditProfileScreen extends StatefulWidget {
  final Map<String, dynamic> userData;
  const EditProfileScreen({super.key, required this.userData});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  // Controller
  late TextEditingController _usernameController;
  late TextEditingController _fullNameController;
  late TextEditingController _phoneController;
  late TextEditingController _addressController;

  // State File & URL
  File? _imageFile;
  String? _currentPhotoUrl;
  bool _isLoading = false;
  final ImagePicker _picker = ImagePicker();
  String _fullPhoneNumber = ""; 

  // State Error
  String? _uError, _fnError, _pError, _aError;

  @override
  void initState() {
    super.initState();
    // Inisialisasi Data dari Database (Port 8000)
    _usernameController = TextEditingController(text: widget.userData['username']);
    _fullNameController = TextEditingController(text: widget.userData['full_name']);
    _addressController = TextEditingController(text: widget.userData['address']);
    _currentPhotoUrl = widget.userData['profile_photo'];
    _fullPhoneNumber = widget.userData['phone'] ?? "";

    // Logika Pembersihan Nomor HP (+62) untuk ModernPhoneInput
    String phoneData = widget.userData['phone'] ?? "";
    String cleanPhone = phoneData.replaceAll("+62", ""); 
    _phoneController = TextEditingController(text: cleanPhone);
  }

  @override
  void dispose() {
    _usernameController.dispose();
    _fullNameController.dispose();
    _phoneController.dispose();
    _addressController.dispose();
    super.dispose();
  }

  // Pilih Foto dari Galeri
  Future<void> _pickImage() async {
    final XFile? selected = await _picker.pickImage(source: ImageSource.gallery, imageQuality: 50);
    if (selected != null) setState(() => _imageFile = File(selected.path));
  }

  // Hapus Foto Profil (Kembali ke Avatar Default)
  void _handleDeletePhoto() async {
    setState(() => _isLoading = true);
    final result = await ApiServices.deleteProfilePhoto();
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      setState(() {
        _imageFile = null;
        // Avatar Inisial UI-Avatars
        _currentPhotoUrl = "https://ui-avatars.com/api/?name=${_fullNameController.text}&background=00197D&color=fff";
      });
      ModernNotify.show(context, "Foto berhasil dihapus", isError: false);
    } else {
      ModernNotify.show(context, "Gagal menghapus foto profil");
    }
  }

  // Simpan Perubahan
  void _handleUpdate() async {
    setState(() {
      _uError = _usernameController.text.isEmpty ? "Username wajib diisi" : null;
      _fnError = _fullNameController.text.isEmpty ? "Nama wajib diisi" : null;
    });

    if (_uError != null || _fnError != null) {
      ModernNotify.show(context, "Nama dan Username tidak boleh kosong");
      return;
    }

    setState(() => _isLoading = true);
    final result = await ApiServices.updateProfile(
      username: _usernameController.text,
      fullName: _fullNameController.text,
      phone: _fullPhoneNumber, 
      address: _addressController.text,
      imageFile: _imageFile, 
    );
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      await prefs.setString('full_name', _fullNameController.text);
      
      ModernNotify.show(context, "Profil berhasil diperbarui!", isError: false);
      
      // Jeda agar user melihat notifikasi sebelum kembali
      Future.delayed(const Duration(milliseconds: 1500), () {
        if (mounted) Navigator.pop(context, true); 
      });
    } else {
      ModernNotify.show(context, result['message'] ?? "Gagal update profil");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      extendBodyBehindAppBar: true, 
      appBar: AppBar(
        title: const Text("Kelola Profil", style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // --- HEADER GRADIENT NAVY ---
            Container(
              width: double.infinity,
              padding: EdgeInsets.only(top: MediaQuery.of(context).padding.top + 60, bottom: 45),
              decoration: const BoxDecoration(
                gradient: AppTheme.headerGradient,
                borderRadius: BorderRadius.only(bottomLeft: Radius.circular(50), bottomRight: Radius.circular(50)),
                boxShadow: [BoxShadow(color: Colors.black26, blurRadius: 15, offset: Offset(0, 8))]
              ),
              child: Column(
                children: [
                  // Avatar Container
                  Center(
                    child: Stack(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(4),
                          decoration: BoxDecoration(
                            shape: BoxShape.circle, 
                            border: Border.all(color: Colors.white.withOpacity(0.4), width: 3)
                          ),
                          child: CircleAvatar(
                            radius: 65,
                            backgroundColor: Colors.white,
                            backgroundImage: _imageFile != null
                                ? FileImage(_imageFile!) as ImageProvider<Object>?
                                : (_currentPhotoUrl != null ? NetworkImage(_currentPhotoUrl!) as ImageProvider<Object>? : null),
                            child: (_imageFile == null && _currentPhotoUrl == null)
                                ? const Icon(Icons.person, size: 70, color: AppTheme.primaryBlue)
                                : null,
                          ),
                        ),
                        // Tombol Kamera (Edit)
                        Positioned(
                          bottom: 5, right: 5,
                          child: GestureDetector(
                            onTap: _pickImage,
                            child: const CircleAvatar(
                              backgroundColor: AppTheme.goldAccent, 
                              radius: 20, 
                              child: Icon(Icons.camera_alt, color: AppTheme.primaryBlue, size: 20)
                            ),
                          ),
                        ),
                        // Tombol Hapus (Merah) - Muncul hanya jika ada foto non-default
                        if ((_currentPhotoUrl != null && !_currentPhotoUrl!.contains('ui-avatars')) || _imageFile != null)
                          Positioned(
                            top: 5, right: 5,
                            child: GestureDetector(
                              onTap: _handleDeletePhoto,
                              child: const CircleAvatar(
                                backgroundColor: Colors.red, 
                                radius: 18, 
                                child: Icon(Icons.delete_forever, color: Colors.white, size: 18)
                              ),
                            ),
                          ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            const EventHeader(), // Banner Event di atas Form

            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 30, vertical: 20),
              child: Column(
                children: [
                  // FORM INPUT MODERN
                  ModernInput(controller: _usernameController, label: "USERNAME", hint: "Username", icon: Icons.alternate_email, isRequired: true, errorText: _uError),
                  const SizedBox(height: 18),
                  
                  ModernInput(controller: _fullNameController, label: "NAMA LENGKAP", hint: "Nama lengkap", icon: Icons.badge_outlined, isRequired: true, errorText: _fnError),
                  const SizedBox(height: 18),
                  
                  ModernPhoneInput(
                    controller: _phoneController,
                    label: "NOMOR HANDPHONE",
                    errorText: _pError,
                    onNumberChanged: (val) => _fullPhoneNumber = val,
                  ),
                  const SizedBox(height: 18),
                  
                  ModernInput(controller: _addressController, label: "ALAMAT DOMISILI", hint: "Alamat lengkap", icon: Icons.map_outlined, errorText: _aError, maxLines: 3),
                  
                  const SizedBox(height: 40),

                  // TOMBOL SIMPAN
                  _isLoading
                      ? const CircularProgressIndicator(color: AppTheme.primaryBlue)
                      : SizedBox(
                          width: double.infinity,
                          height: 55,
                          child: ElevatedButton(
                            onPressed: _handleUpdate,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppTheme.goldAccent,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                              elevation: 5,
                              shadowColor: AppTheme.goldAccent.withOpacity(0.3),
                            ),
                            child: const Text(
                              "SIMPAN PERUBAHAN", 
                              style: TextStyle(color: AppTheme.primaryBlue, fontWeight: FontWeight.bold, fontSize: 16)
                            ),
                          ),
                        ),
                  const SizedBox(height: 50),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}