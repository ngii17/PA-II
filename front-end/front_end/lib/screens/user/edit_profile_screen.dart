import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:provider/provider.dart';

import '../../services/api_services.dart';
import '../../providers/event_provider.dart';
import '../event/event_header.dart';

class EditProfileScreen extends StatefulWidget {
  final Map<String, dynamic> userData;
  const EditProfileScreen({super.key, required this.userData});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  late TextEditingController _usernameController;
  late TextEditingController _fullNameController;
  late TextEditingController _addressController;
  late TextEditingController _phoneController;

  String? _selectedAvatar;
  String? _currentPhotoUrl;
  bool _isLoading = false;

  String _fullPhoneNumber = "";
  String? _uError, _fnError;

  final List<String> _avatarList = [
    'avatar1.png', 'avatar2.png', 'avatar3.png', 'avatar4.png',
    'avatar5.png', 'avatar6.png', 'avatar7.png', 'avatar8.png',
  ];

  @override
  void initState() {
    super.initState();
    _usernameController = TextEditingController(text: widget.userData['username']);
    _fullNameController = TextEditingController(text: widget.userData['full_name']);
    _addressController = TextEditingController(text: widget.userData['address']);
    _currentPhotoUrl = widget.userData['profile_photo'];
    _selectedAvatar = widget.userData['profile_photo'];

    _fullPhoneNumber = widget.userData['phone'] ?? "";
    String cleanPhone = _fullPhoneNumber.replaceAll("+62", "");
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

  void _showSnackBar(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(msg), backgroundColor: color, behavior: SnackBarBehavior.floating),
    );
  }

  void _handleUpdate() async {
    setState(() {
      _uError = _usernameController.text.isEmpty ? "Username tidak boleh kosong" : null;
      _fnError = _fullNameController.text.isEmpty ? "Nama lengkap tidak boleh kosong" : null;
    });
    if (_uError != null || _fnError != null) {
      _showSnackBar("Nama dan Username harus diisi", Colors.red);
      return;
    }

    setState(() => _isLoading = true);
    final result = await ApiServices.updateProfile(
      username: _usernameController.text,
      fullName: _fullNameController.text,
      phone: _fullPhoneNumber,
      address: _addressController.text,
      avatar: _selectedAvatar,
    );
    setState(() => _isLoading = false);

    if (result['success'] == true) {
      final SharedPreferences prefs = await SharedPreferences.getInstance();
      await prefs.setString('full_name', _fullNameController.text);
      _showSnackBar("Profil berhasil diperbarui!", Colors.green);
      if (mounted) Navigator.pop(context, true);
    } else {
      _showSnackBar(result['message'] ?? "Gagal update profil", Colors.red);
    }
  }

  String _getAvatarUrl(String? avatarNameOrUrl) {
    if (avatarNameOrUrl == null || avatarNameOrUrl.isEmpty) {
      return '';
    }

    // Jika sudah full URL (dimulai dengan http), pakai langsung
    if (avatarNameOrUrl.startsWith('http')) {
      return avatarNameOrUrl;
    }

    // Jika hanya nama file (avatar3.png), construct URL
    // Sumber IP sekarang terpusat di ApiServices, bukan ProfileScreen lagi
    return 'https://purnama-hotel.duckdns.org/auth/avatars/$avatarNameOrUrl';  

  }

  void _showAvatarPicker(Color primaryColor, Color buttonColor, Color buttonTextColor) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (context) => Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text("Pilih Avatar", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: primaryColor)),
            const SizedBox(height: 20),
            GridView.builder(
              shrinkWrap: true,
              itemCount: _avatarList.length,
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 4,
                crossAxisSpacing: 12,
                mainAxisSpacing: 12,
              ),
              itemBuilder: (context, index) {
                final avatar = _avatarList[index];
                final isSelected = _selectedAvatar == avatar;
                return GestureDetector(
                  onTap: () {
                    setState(() => _selectedAvatar = avatar);
                    Navigator.pop(context);
                  },
                  child: Container(
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(
                        color: isSelected ? primaryColor : Colors.transparent,
                        width: 3,
                      ),
                    ),
                    child: CircleAvatar(
                      backgroundColor: Colors.grey.shade200,
                      backgroundImage: NetworkImage(_getAvatarUrl(avatar)),
                      onBackgroundImageError: (exception, stackTrace) {
                        debugPrint('Gagal load avatar $avatar: $exception');
                      },
                    ),
                  ),
                );
              },
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final eventProvider = context.watch<EventProvider>();
    final bool hasEvent = eventProvider.eventCode != 'default';
    final Color primaryColor = hasEvent ? eventProvider.primaryColor : const Color(0xFF00197D);
    final Color secondaryColor = hasEvent ? eventProvider.secondaryColor : const Color(0xFFD4AF37);
    final Color onPrimary = primaryColor.computeLuminance() > 0.5 ? Colors.black87 : Colors.white;

    final Color buttonColor = (primaryColor.computeLuminance() - secondaryColor.computeLuminance()).abs() < 0.08
        ? primaryColor
        : secondaryColor;
    final Color buttonTextColor = buttonColor.computeLuminance() > 0.5 ? Colors.black87 : Colors.white;

    final LinearGradient headerGradient = LinearGradient(
      begin: Alignment.topLeft,
      end: Alignment.bottomRight,
      colors: [primaryColor, secondaryColor.withOpacity(0.85)],
    );

    // URL avatar yang sedang dipilih
    final String? avatarUrl = _selectedAvatar != null
        ? _getAvatarUrl(_selectedAvatar)
        : _currentPhotoUrl;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text("Kelola Profil", style: TextStyle(fontWeight: FontWeight.bold)),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: IconThemeData(color: onPrimary),
        foregroundColor: onPrimary,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Header Gradien dengan Avatar
            Container(
              width: double.infinity,
              padding: EdgeInsets.only(
                top: MediaQuery.of(context).padding.top + 60,
                bottom: 40,
              ),
              decoration: BoxDecoration(
                gradient: headerGradient,
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(50),
                  bottomRight: Radius.circular(50),
                ),
                boxShadow: [
                  BoxShadow(
                    color: primaryColor.withOpacity(0.3),
                    blurRadius: 20,
                    offset: const Offset(0, 10),
                  ),
                ],
              ),
              child: Center(
                child: Stack(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(4),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(color: onPrimary.withOpacity(0.8), width: 2),
                      ),
                      child: CircleAvatar(
                        radius: 65,
                        backgroundColor: Colors.white,
                        backgroundImage: avatarUrl != null && avatarUrl.isNotEmpty
                            ? NetworkImage('$avatarUrl?t=${DateTime.now().millisecondsSinceEpoch}')
                            : null,
                        child: (avatarUrl == null || avatarUrl.isEmpty)
                            ? Icon(Icons.person, size: 70, color: primaryColor)
                            : null,
                      ),
                    ),
                    // Tombol ganti avatar
                    Positioned(
                      bottom: 4,
                      right: 4,
                      child: GestureDetector(
                        onTap: () => _showAvatarPicker(primaryColor, buttonColor, buttonTextColor),
                        child: Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: buttonColor,
                            shape: BoxShape.circle,
                            border: Border.all(color: Colors.white, width: 2),
                            boxShadow: [BoxShadow(color: primaryColor.withOpacity(0.5), blurRadius: 8)],
                          ),
                          child: Icon(Icons.face_retouching_natural, color: buttonTextColor, size: 20),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const EventHeader(),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
              child: Column(
                children: [
                  _buildTextField(
                    controller: _usernameController,
                    label: "USERNAME",
                    icon: Icons.alternate_email,
                    errorText: _uError,
                    primaryColor: primaryColor,
                  ),
                  const SizedBox(height: 16),
                  _buildTextField(
                    controller: _fullNameController,
                    label: "NAMA LENGKAP",
                    icon: Icons.person_outline,
                    errorText: _fnError,
                    primaryColor: primaryColor,
                  ),
                  const SizedBox(height: 16),
                  _buildPhoneField(),
                  const SizedBox(height: 16),
                  _buildTextField(
                    controller: _addressController,
                    label: "ALAMAT DOMISILI",
                    icon: Icons.location_on_outlined,
                    primaryColor: primaryColor,
                    maxLines: 3,
                  ),
                  const SizedBox(height: 36),
                  _isLoading
                      ? CircularProgressIndicator(color: primaryColor)
                      : SizedBox(
                          width: double.infinity,
                          child: ElevatedButton(
                            onPressed: _handleUpdate,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: buttonColor,
                              foregroundColor: buttonTextColor,
                              padding: const EdgeInsets.symmetric(vertical: 16),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                            ),
                            child: const Text(
                              "SIMPAN PERUBAHAN",
                              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
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

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    String? errorText,
    required Color primaryColor,
    int maxLines = 1,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.grey.shade700, letterSpacing: 0.8)),
        const SizedBox(height: 6),
        TextField(
          controller: controller,
          maxLines: maxLines,
          decoration: InputDecoration(
            hintText: label,
            prefixIcon: Icon(icon, color: primaryColor),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: BorderSide(color: primaryColor, width: 2),
            ),
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            errorText: errorText,
          ),
        ),
      ],
    );
  }

  Widget _buildPhoneField() {
    final Color primaryColor = context.watch<EventProvider>().primaryColor;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text("NOMOR HANDPHONE", style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.grey.shade700, letterSpacing: 0.8)),
        const SizedBox(height: 6),
        Row(
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: Colors.grey.shade300),
              ),
              child: const Text("+62", style: TextStyle(fontWeight: FontWeight.bold)),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: TextField(
                controller: _phoneController,
                keyboardType: TextInputType.phone,
                decoration: InputDecoration(
                  hintText: "81234567890",
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: BorderSide(color: primaryColor, width: 2),
                  ),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                ),
                onChanged: (value) {
                  _fullPhoneNumber = "+62${value.replaceAll(RegExp(r'[^0-9]'), '')}";
                },
              ),
            ),
          ],
        ),
      ],
    );
  }
}