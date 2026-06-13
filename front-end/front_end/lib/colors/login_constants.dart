import 'package:flutter/material.dart';

class AppTheme {
  // =========================
  // DEFAULT COLORS
  // =========================
  static const Color primaryBlue = Color(0xFF00197D);
  static const Color goldAccent = Color(0xFFD4AF37);
  static const Color lightBackground = Color(0xFFF8F9FA);

  // =========================
  // DEFAULT GRADIENTS
  // =========================
  static const LinearGradient headerGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [
      Color(0xFF00197D),
      Color(0xFF1A3A9C),
    ],
  );

  static const LinearGradient restoGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      goldAccent,
      Color(0xFFFFE9B3),
    ],
  );

  // =========================
  // HEX TO COLOR
  // =========================
  static Color hexToColor(String hexString) {
    final buffer = StringBuffer();

    if (hexString.length == 6 || hexString.length == 7) {
      buffer.write('ff');
    }

    buffer.write(hexString.replaceFirst('#', ''));

    return Color(
      int.parse(
        buffer.toString(),
        radix: 16,
      ),
    );
  }

  // =========================
  // LIGHT / DARK DETECTION
  // =========================
  static bool isLightColor(Color color) {
    return color.computeLuminance() > 0.5;
  }

  static Color contrastColor(Color bg) {
    return isLightColor(bg)
        ? Colors.black87
        : Colors.white;
  }

  // =========================
  // COLOR HELPERS
  // =========================
  static Color lighten(Color color, double amount) {
    final hsl = HSLColor.fromColor(color);

    return hsl
        .withLightness(
          (hsl.lightness + amount).clamp(0.0, 1.0),
        )
        .toColor();
  }

  static Color darken(Color color, double amount) {
    final hsl = HSLColor.fromColor(color);

    return hsl
        .withLightness(
          (hsl.lightness - amount).clamp(0.0, 1.0),
        )
        .toColor();
  }

  // =========================
  // EVENT GRADIENT
  // =========================
  static LinearGradient buildEventGradient(
    Color primary,
    Color secondary,
  ) {
    final darkPrimary = darken(primary, 0.15);

    return LinearGradient(
      begin: Alignment.topLeft,
      end: Alignment.bottomRight,
      colors: [
        primary,
        darkPrimary,
      ],
    );
  }

  // =========================
  // HEADER GRADIENT
  // =========================
  static LinearGradient buildHeaderGradient(
    Color primary,
    Color secondary,
  ) {
    return LinearGradient(
      begin: Alignment.topCenter,
      end: Alignment.bottomCenter,
      colors: [
        primary,
        Color.lerp(
              primary,
              secondary,
              0.25,
            ) ??
            primary,
      ],
    );
  }

  // =========================
  // CONTRAST RATIO
  // =========================
  static double _contrastRatio(
    Color a,
    Color b,
  ) {
    final la = a.computeLuminance() + 0.05;
    final lb = b.computeLuminance() + 0.05;

    return la > lb
        ? la / lb
        : lb / la;
  }

  // =========================
  // BUTTON COLORS
  // =========================
  static Color resolveButtonColor(
    Color primaryBg,
    Color secondary,
  ) {
    final contrast =
        _contrastRatio(primaryBg, secondary);

    if (contrast >= 3.0) {
      return secondary;
    }

    return isLightColor(primaryBg)
        ? Colors.black87
        : Colors.white;
  }

  static Color resolveButtonTextColor(
    Color buttonColor,
  ) {
    return isLightColor(buttonColor)
        ? primaryBlue
        : Colors.white;
  }

  // =========================
  // GENERATE THEME
  // =========================
  static ThemeData getTheme(
    Map<String, dynamic> themeData,
  ) {
    final primaryColor = hexToColor(
      themeData['primary_color'] ?? '#00197D',
    );

    final secondaryColor = hexToColor(
      themeData['secondary_color'] ?? '#D4AF37',
    );

    final primaryTextColor =
        contrastColor(primaryColor);

    final secondaryTextColor =
        contrastColor(secondaryColor);

    return ThemeData(
      useMaterial3: true,

      colorScheme: ColorScheme.fromSeed(
        seedColor: primaryColor,
        primary: primaryColor,
        secondary: secondaryColor,
        brightness: Brightness.light,
      ),

      scaffoldBackgroundColor:
          lightBackground,

      appBarTheme: AppBarTheme(
        backgroundColor: primaryColor,
        foregroundColor: primaryTextColor,
        centerTitle: true,
        elevation: 4,
        toolbarHeight: 70,
      ),

      elevatedButtonTheme:
          ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: secondaryColor,
          foregroundColor: secondaryTextColor,
          elevation: 6,
          padding: const EdgeInsets.symmetric(
            horizontal: 28,
            vertical: 14,
          ),
          shape: RoundedRectangleBorder(
            borderRadius:
                BorderRadius.circular(14),
          ),
          textStyle: const TextStyle(
            fontWeight: FontWeight.w700,
            fontSize: 14,
            letterSpacing: 0.5,
          ),
        ),
      ),

      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: secondaryColor,
          padding:
              const EdgeInsets.symmetric(
            horizontal: 16,
            vertical: 12,
          ),
          shape: RoundedRectangleBorder(
            borderRadius:
                BorderRadius.circular(12),
          ),
        ),
      ),

      outlinedButtonTheme:
          OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: secondaryColor,
          side: BorderSide(
            color: secondaryColor,
            width: 2.5,
          ),
          padding:
              const EdgeInsets.symmetric(
            horizontal: 28,
            vertical: 14,
          ),
          shape: RoundedRectangleBorder(
            borderRadius:
                BorderRadius.circular(14),
          ),
        ),
      ),

      inputDecorationTheme:
          InputDecorationTheme(
        filled: true,
        fillColor: Colors.white,
        contentPadding:
            const EdgeInsets.symmetric(
          horizontal: 18,
          vertical: 14,
        ),

        border: OutlineInputBorder(
          borderRadius:
              BorderRadius.circular(14),
          borderSide: BorderSide(
            color:
                primaryColor.withAlpha(102),
            width: 1.5,
          ),
        ),

        enabledBorder: OutlineInputBorder(
          borderRadius:
              BorderRadius.circular(14),
          borderSide: BorderSide(
            color:
                primaryColor.withAlpha(102),
            width: 1.5,
          ),
        ),

        focusedBorder: OutlineInputBorder(
          borderRadius:
              BorderRadius.circular(14),
          borderSide: BorderSide(
            color: secondaryColor,
            width: 2.5,
          ),
        ),

        labelStyle: TextStyle(
          color:
              primaryColor.withAlpha(179),
        ),

        prefixIconColor: primaryColor,
        suffixIconColor: primaryColor,
      ),
    );
  }
}