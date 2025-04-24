<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #f1f8ff;
            font-weight: bold;
        }
        .badge-info {
            background-color: #17a2b8;
        }
        .badge-success {
            background-color: #28a745;
        }
        .document-link {
            margin-right: 10px;
        }
        .student-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center">Student Information</h1>
                <p class="text-center text-muted">Showing {{ $students->count() }} students</p>
                <hr>
            </div>
        </div>

        <div class="row">
            @foreach($students as $student)
            <div class="col-12 col-md-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            {{ $student->full_name }}
                            @if($student->id_pfe)
                            <span class="badge bg-secondary ms-2">ID: {{ $student->id_pfe }}</span>
                            @endif
                        </div>
                        @if($student->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-3">
                                <img class="student-avatar" 
                                     src="{{ $student->photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) }}" 
                                     alt="{{ $student->full_name }}">
                            </div>
                            <div class="col-9">
                                <p class="mb-1"><strong>Email:</strong> {{ $student->email }}</p>
                                @if($student->email_perso)
                                <p class="mb-1"><strong>Personal Email:</strong> {{ $student->email_perso }}</p>
                                @endif
                                @if($student->phone)
                                <p class="mb-1"><strong>Phone:</strong> {{ $student->phone }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6>Academic Information</h6>
                                <p class="mb-1"><strong>Level:</strong> <span class="badge bg-info">{{ $student->level }}</span></p>
                                @if($student->program)
                                <p class="mb-1"><strong>Program:</strong> <span class="badge bg-primary">{{ $student->program }}</span></p>
                                @endif
                                <p class="mb-1"><strong>Academic Year:</strong> {{ $student->year->title ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <h6>Documents</h6>
                                @if($student->cv)
                                <a href="{{ $student->cv }}" target="_blank" class="btn btn-sm btn-outline-primary document-link">
                                    <i class="bi bi-file-earmark-pdf"></i> CV
                                </a>
                                @endif
                                
                                @if($student->lm)
                                <a href="{{ $student->lm }}" target="_blank" class="btn btn-sm btn-outline-secondary document-link">
                                    <i class="bi bi-file-earmark-text"></i> Cover Letter
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
