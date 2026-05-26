@extends('dashboard.layouts.app')
@section('title', 'Manajemen Pengguna')
@section('content')

<div class="container-fluid px-4">
    {{-- Header & Stats Section --}}
    <div class="row mb-4 align-items-end">
        <div class="col-lg-6">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="bg-primary text-white d-flex align-items-center justify-content-center shadow-sm"
                     style="width: 45px; height: 45px; border-radius: 12px;">
                    <i class="fas fa-users-cog fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Manajemen Pengguna</h4>
                    <p class="text-muted small mb-0">Kelola otoritas dan pantau aktivitas akun dalam ekosistem Purnama.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="d-flex flex-column flex-md-row justify-content-lg-end gap-3">
                {{-- Stat Card Ringkas --}}
                <div class="bg-white px-3 py-2 shadow-sm d-flex align-items-center gap-3" style="border-radius: 12px; border-right: 4px solid #0d6efd;">
                    <div class="text-primary bg-primary bg-opacity-10 p-2 rounded-circle">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Total Pengguna</div>
                        <div class="fw-bold" style="font-size: 18px;">{{ count($users) }} <span class="text-muted" style="font-size: 12px; font-weight: normal;">Akun</span></div>
                    </div>
                </div>

                {{-- Filter Dropdown --}}
                <div class="position-relative">
                    <form action="{{ route('dashboard.pengguna') }}" method="GET" id="filterForm">
                        <label class="small fw-bold text-muted mb-1 d-block" style="letter-spacing: 0.5px;">URUTKAN BERDASARKAN PERAN:</label>
                        <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-filter text-muted"></i></span>
                            <select name="role" class="form-select border-0 fw-medium"
                                    style="padding: 10px 15px; cursor: pointer; min-width: 200px;"
                                    onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua Peran</option>
                                <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>🛡️ Administrator</option>
                                <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>👤 Pelanggan (Customer)</option>
                                <option value="3" {{ request('role') == '3' ? 'selected' : '' }}>🏨 Staf Hotel</option>
                                <option value="4" {{ request('role') == '4' ? 'selected' : '' }}>🍽️ Staf Restoran</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background: linear-gradient(to right, #1a1a2e, #16213e); color: #ffffff;">
                            <th class="border-0 px-4 py-4 text-center" style="width: 60px; font-size: 11px; letter-spacing: 1px;">NO</th>
                            <th class="border-0 py-4" style="font-size: 11px; letter-spacing: 1px;">PROFIL PENGGUNA</th>
                            <th class="border-0 py-4" style="font-size: 11px; letter-spacing: 1px;">DETAIL KONTAK</th>
                            <th class="border-0 py-4 text-center" style="font-size: 11px; letter-spacing: 1px;">ROLE / HAK AKSES</th>
                            <th class="border-0 py-4 text-center" style="font-size: 11px; letter-spacing: 1px;">STATUS VERIFIKASI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $i => $u)
                        @php
                            $roleId = $u['role_id'] ?? 0;
                            $roles = [
                                1 => ['name' => 'ADMIN', 'color' => '#dc3545', 'icon' => 'fa-user-shield', 'bg' => '#fff5f5'],
                                2 => ['name' => 'CUSTOMER', 'color' => '#0d6efd', 'icon' => 'fa-user', 'bg' => '#f0f7ff'],
                                3 => ['name' => 'STAFF HOTEL', 'color' => '#0dcaf0', 'icon' => 'fa-hotel', 'bg' => '#f0fcff'],
                                4 => ['name' => 'STAFF RESTO', 'color' => '#ffc107', 'icon' => 'fa-utensils', 'bg' => '#fffdf0'],
                            ];
                            $currentRole = $roles[$roleId] ?? ['name' => 'GUEST', 'color' => '#6c757d', 'icon' => 'fa-question', 'bg' => '#f8f9fa'];
                        @endphp
                        <tr>
                            <td class="text-center">
                                <span class="text-muted fw-bold" style="font-size: 13px;">{{ $i + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm"
                                             style="width: 42px; height: 42px; font-size: 15px; background: {{ $currentRole['color'] }}; border: 3px solid #fff;">
                                            {{ strtoupper(substr($u['full_name'] ?? 'U', 0, 1)) }}
                                        </div>
                                        @if($u['is_verified'])
                                        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle"
                                              style="width: 12px; height: 12px;" title="Verified"></span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 14px;">{{ $u['full_name'] ?? 'Guest User' }}</div>
                                        <div class="text-muted" style="font-size: 12px;">{{ '@' . ($u['username'] ?? 'username') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium text-dark" style="font-size: 13px;">{{ $u['email'] }}</span>
                                    <span class="text-muted" style="font-size: 11px;"><i class="fas fa-phone-alt me-1"></i> {{ $u['phone'] ?? 'Tidak ada kontak' }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge px-3 py-2"
                                      style="background-color: {{ $currentRole['bg'] }}; color: {{ $currentRole['color'] }}; border: 1px solid {{ $currentRole['color'] }}40; font-size: 10px; border-radius: 8px; font-weight: 800;">
                                    <i class="fas {{ $currentRole['icon'] }} me-1"></i> {{ $currentRole['name'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($u['is_verified'] ?? false)
                                    <div class="d-inline-flex align-items-center gap-1 text-success bg-success bg-opacity-10 px-2 py-1 rounded-pill" style="font-size: 11px; font-weight: 700;">
                                        <i class="fas fa-check-double"></i> TERVERIFIKASI
                                    </div>
                                @else
                                    <div class="d-inline-flex align-items-center gap-1 text-secondary bg-light px-2 py-1 rounded-pill" style="font-size: 11px; font-weight: 700; border: 1px solid #e0e0e0;">
                                        <i class="fas fa-clock"></i> PENDING
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-25">
                                <h6 class="text-muted fw-light">Tidak ada data pengguna dalam kategori ini.</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Styling tambahan untuk table hover */
    .table-hover tbody tr:hover {
        background-color: #f8faff !important;
        transition: all 0.2s ease;
    }
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
    .badge {
        letter-spacing: 0.5px;
    }
</style>

@endsection
