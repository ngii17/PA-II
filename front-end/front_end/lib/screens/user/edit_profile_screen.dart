import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';

class EditProfileScreen extends StatefulWidget {
  final Map<String, dynamic> userData; // Menerima data user yang sekarang

  const EditProfileScreen({super.key, required this.userData});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  // 1. Controller untuk form
  late TextEditingController _usernameController;
  late TextEditingController _fullNameController;
  late TextEditingController _phoneController;
  late TextEditingController _addressController;

  File? _imageFile; // Menyimpan file foto yang baru dipilih
  String? _currentPhotoUrl; // Menyimpan URL foto yang sedang tampil
  bool _isLoading = false;
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    // Isi otomatis form dengan data yang ada sekarang
    _usernameController = TextEditingController(text: widget.userData['username']);
    _fullNameController = TextEditingController(text: widget.userData['full_name']);
    _phoneController = TextEditingController(text: widget.userData['phone']);
    _addressController = TextEditingController(text: widget.userData['address']);
    _currentPhotoUrl = widget.userData['profile_photo'];
  }

  // 2. Fungsi untuk membuka Galeri
  Future<void> _pickImage() async {
    final XFile? selected = await _picker.pickImage(
      source: ImageSource.gallery,
      imageQuality: 50,
    );

    if (selected != null) {
      setState(() {
        _imageFile = File(selected.path);
      });
    }
  }

  // 3. Fungsi Hapus Foto (Kembali ke Avatar Default)
  void _handleDeletePhoto() async {
    setState(() => _isLoading = true);

    final result = await ApiServices.deleteProfilePhoto();

    setState(() => _isLoading = false);

    if (result['success'] == true) {
      setState(() {
        _imageFile = null;
        // Kita paksa tampilan ke UI-Avatars (Avatar inisial)
        _currentPhotoUrl = "https://ui-avatars.com/api/?name=${_fullNameController.text}&background=0D8ABC&color=fff";
      });
      _showSnackBar("Foto dihapus, kembali ke avatar default", Colors.green);
    } else {
      _showSnackBar(result['message'] ?? "Gagal menghapus foto", Colors.red);
    }
  }

  // 4. Fungsi Simpan Perubahan
  void _handleUpdate() async {
    if (_usernameController.text.isEmpty || _fullNameController.text.isEmpty) {
      _showSnackBar("Nama dan Username tidak boleh kosong", Colors.red);
      return;
    }

    setState(() => _isLoading = true);

    final result = await ApiServices.updateProfile(
      username: _usernameController.text,
      fullName: _fullNameController.text,
      phone: _phoneController.text,
      address: _addressController.text,
      imageFile: _imageFile, 
    );

    setState(() => _isLoading = false);

    if (result['success'] == true) {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      await prefs.setString('full_name', _fullNameController.text);
      
      _showSnackBar("Profil berhasil diperbarui!", Colors.green);
      
      // Kembali ke halaman sebelumnya dan mengirim sinyal 'true' untuk refresh data
      if (mounted) Navigator.pop(context, true);
    } else {
      _showSnackBar(result['message'] ?? "Gagal update profil", Colors.red);
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(msg), backgroundColor: color),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Kelola Profil"),
        backgroundColor: Colors.blueAccent,
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            // --- BAGIAN FOTO PROFIL DENGAN TOMBOL HAPUS ---
            Center(
              child: Stack(
                children: [
                  CircleAvatar(
                    radius: 65,
                    backgroundColor: Colors.blueAccent.withOpacity(0.1),
                    backgroundImage: _imageFile != null
                        ? FileImage(_imageFile!)
                        : (_currentPhotoUrl != null
                            ? NetworkImage(_currentPhotoUrl!) as ImageProvider
                            : null),
                    child: (_imageFile == null && _currentPhotoUrl == null)
                        ? const Icon(Icons.person, size: 60)
                        : null,
                  ),
                  // Tombol Kamera (Ganti Foto)
                  Positioned(
                    bottom: 0,
                    right: 0,
                    child: InkWell(
                      onTap: _pickImage,
                      child: const CircleAvatar(
                        backgroundColor: Colors.blueAccent,
                        radius: 20,
                        child: Icon(Icons.camera_alt, color: Colors.white, size: 20),
                      ),
                    ),
                  ),
                  // Tombol Tong Sampah (Hapus Foto)
                  // Muncul hanya jika ada foto asli (bukan ui-avatars)
                  if (_currentPhotoUrl != null && !_currentPhotoUrl!.contains('ui-avatars') || _imageFile != null)
                    Positioned(
                      top: 0,
                      right: 0,
                      child: InkWell(
                        onTap: _handleDeletePhoto,
                        child: const CircleAvatar(
                          backgroundColor: Colors.red,
                          radius: 20,
                          child: Icon(Icons.delete_forever, color: Colors.white, size: 20),
                        ),
                      ),
                    ),
                ],
              ),
            ),
            const SizedBox(height: 30),

            // --- FORM INPUT ---
            _buildTextField(_usernameController, "Username", Icons.alternate_email),
            const SizedBox(height: 15),
            _buildTextField(_fullNameController, "Nama Lengkap", Icons.person_outline),
            const SizedBox(height: 15),
            _buildTextField(_phoneController, "Nomor HP", Icons.phone_android),
            const SizedBox(height: 15),
            _buildTextField(_addressController, "Alamat Lengkap", Icons.location_on_outlined, maxLines: 3),
            const SizedBox(height: 40),

            _isLoading
                ? const CircularProgressIndicator()
                : SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: _handleUpdate,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.blueAccent,
                        padding: const EdgeInsets.all(15),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                      child: const Text(
                        "SIMPAN PERUBAHAN", 
                        style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16)
                      ),
                    ),
                  ),
          ],
        ),
      ),
    );
  }

  // Widget pembantu untuk merapikan TextField
  Widget _buildTextField(TextEditingController controller, String label, IconData icon, {int maxLines = 1}) {
    return TextField(
      controller: controller,
      maxLines: maxLines,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
      ),
    );
  }
}