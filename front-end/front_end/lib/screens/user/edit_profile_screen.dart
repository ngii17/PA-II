import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_services.dart';
import '../event/event_header.dart'; // <--- IMPORT HEADER EVENT

class EditProfileScreen extends StatefulWidget {
  final Map<String, dynamic> userData;

  const EditProfileScreen({super.key, required this.userData});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  late TextEditingController _usernameController;
  late TextEditingController _fullNameController;
  late TextEditingController _phoneController;
  late TextEditingController _addressController;

  File? _imageFile;
  String? _currentPhotoUrl;
  bool _isLoading = false;
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    _usernameController = TextEditingController(text: widget.userData['username']);
    _fullNameController = TextEditingController(text: widget.userData['full_name']);
    _phoneController = TextEditingController(text: widget.userData['phone']);
    _addressController = TextEditingController(text: widget.userData['address']);
    _currentPhotoUrl = widget.userData['profile_photo'];
  }

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

  void _handleDeletePhoto() async {
    setState(() => _isLoading = true);
    final result = await ApiServices.deleteProfilePhoto();
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      setState(() {
        _imageFile = null;
        _currentPhotoUrl = "https://ui-avatars.com/api/?name=${_fullNameController.text}&background=0D8ABC&color=fff";
      });
      _showSnackBar("Foto dihapus, kembali ke avatar default", Colors.green);
    } else {
      _showSnackBar(result['message'] ?? "Gagal menghapus foto", Colors.red);
    }
  }

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
      if (mounted) Navigator.pop(context, true);
    } else {
      String errorMsg = result['message'] ?? "Gagal update profil";
      if (result['errors'] != null) {
        var errors = result['errors'] as Map<String, dynamic>;
        errorMsg = errors.values.first[0]; 
      }
      _showSnackBar(errorMsg, Colors.red);
    }
  }

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(msg), backgroundColor: color, behavior: SnackBarBehavior.floating),
    );
  }

  @override
  Widget build(BuildContext context) {
    // KUNCI: Ambil warna tema aktif
    final primaryColor = Theme.of(context).primaryColor;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Kelola Profil"),
        backgroundColor: primaryColor, // Ikuti tema
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // --- 1. BANNER EVENT (Paling Atas) ---
            const EventHeader(),

            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                children: [
                  // --- 2. BAGIAN FOTO PROFIL ---
                  Center(
                    child: Stack(
                      children: [
                        CircleAvatar(
                          radius: 65,
                          backgroundColor: primaryColor.withOpacity(0.1),
                          backgroundImage: _imageFile != null
                              ? FileImage(_imageFile!)
                              : (_currentPhotoUrl != null
                                  ? NetworkImage(_currentPhotoUrl!) as ImageProvider
                                  : null),
                          child: (_imageFile == null && _currentPhotoUrl == null)
                              ? Icon(Icons.person, size: 60, color: primaryColor)
                              : null,
                        ),
                        // Tombol Kamera
                        Positioned(
                          bottom: 0,
                          right: 0,
                          child: InkWell(
                            onTap: _pickImage,
                            child: CircleAvatar(
                              backgroundColor: primaryColor,
                              radius: 20,
                              child: const Icon(Icons.camera_alt, color: Colors.white, size: 20),
                            ),
                          ),
                        ),
                        // Tombol Hapus Foto
                        if ((_currentPhotoUrl != null && !_currentPhotoUrl!.contains('ui-avatars')) || _imageFile != null)
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
                  const SizedBox(height: 35),

                  // --- 3. FORM INPUT (Warna Fokus Ikuti Tema) ---
                  _buildTextField(_usernameController, "Username", Icons.alternate_email, primaryColor),
                  const SizedBox(height: 15),
                  _buildTextField(_fullNameController, "Nama Lengkap", Icons.person_outline, primaryColor),
                  const SizedBox(height: 15),
                  _buildTextField(_phoneController, "Nomor HP", Icons.phone_android, primaryColor),
                  const SizedBox(height: 15),
                  _buildTextField(_addressController, "Alamat Lengkap", Icons.location_on_outlined, primaryColor, maxLines: 3),
                  const SizedBox(height: 40),

                  // --- 4. TOMBOL SIMPAN (Ikuti Tema) ---
                  _isLoading
                      ? CircularProgressIndicator(color: primaryColor)
                      : SizedBox(
                          width: double.infinity,
                          child: ElevatedButton(
                            onPressed: _handleUpdate,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: primaryColor,
                              padding: const EdgeInsets.all(18),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
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
          ],
        ),
      ),
    );
  }

  Widget _buildTextField(TextEditingController controller, String label, IconData icon, Color color, {int maxLines = 1}) {
    return TextField(
      controller: controller,
      maxLines: maxLines,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon, color: color),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: color, width: 2), // Garis fokus ikuti tema
        ),
      ),
    );
  }
}