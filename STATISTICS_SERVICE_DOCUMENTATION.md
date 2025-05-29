# Project Statistics Service - Documentation

## Overview

The `ProjectStatisticsService` provides consistent statistical calculations across the Career Center application, ensuring that the integrity check command and the dashboard widgets use the same data calculation methods.

## Key Features

### 1. Unified Department Statistics
- **Professors Count**: Number of active professors per department
- **Projects by Assigned Department**: Projects initially assigned to department
- **Projects by Professor Department**: Projects based on actual supervising/reviewing professors' departments

### 2. Mentoring Statistics
- **Average Supervising**: Average number of supervised projects per professor
- **Average Reviewing**: Average number of reviewed projects per professor  
- **Total Average**: Combined supervision + reviewing average per professor

### 3. Department Assignment Analysis
Identifies mismatches between:
- Projects assigned to departments (for initial supervisor distribution)
- Projects based on professors' actual departments (after reviewers from other departments are added)

## Usage Examples

### In Artisan Commands
```php
use App\Services\ProjectStatisticsService;
use App\Models\Year;

$year = Year::current();
$service = new ProjectStatisticsService($year);

// Get mentoring statistics
$mentoringStats = $service->getMentoringStatisticsSorted();
foreach ($mentoringStats as $stats) {
    echo "{$stats['department_name']}: {$stats['professors_count']} professors, ";
    echo "Avg Supervising: {$stats['avg_supervising']}, ";
    echo "Avg Reviewing: {$stats['avg_reviewing']}, ";
    echo "Total Avg: {$stats['total_avg']}";
}
```

### In Filament Widgets
```php
use App\Services\ProjectStatisticsService;
use App\Models\Year;

protected function getStats(): Collection
{
    $year = Year::current();
    $service = new ProjectStatisticsService($year);
    
    // Get widget statistics with alternative calculation mode
    return $service->getWidgetStatistics($this->showAlternativeStats);
}
```

## Current Implementation Status

### ✅ Completed
1. **ProjectStatisticsService** created with comprehensive methods
2. **CheckProjectStatisticsIntegrity** command updated to use the service
3. **DepartmentAgreementsStatsWidget** updated to use the service
4. **Consistent Results** between command and widget
5. **Department Mismatch Detection** properly identifies assignment discrepancies

### 📊 Current Statistics (2024-2025)
```
EMO: 10 professors, Avg Supervising: 1.6, Avg Reviewing: 8.5, Total Avg: 10.1
SC:  16 professors, Avg Supervising: 2.88, Avg Reviewing: 6.75, Total Avg: 9.63
MIR: 27 professors, Avg Supervising: 4.44, Avg Reviewing: 5.11, Total Avg: 9.55
GLC: 11 professors, Avg Supervising: 2.18, Avg Reviewing: 7.18, Total Avg: 9.36
```

### ⚠️ Known Issues Identified
- **Department Assignment Mismatches**: Projects initially assigned to departments for supervisor distribution, then reviewers added from other departments
- **EMO Department**: 18 assigned vs 67 by professor department
- **MIR Department**: 130 assigned vs 149 by professor department  
- **GLC Department**: 26 assigned vs 82 by professor department
- **SC Department**: 49 assigned vs 110 by professor department

## Service Methods

### Core Statistics
- `getDepartmentStatistics()` - Comprehensive department statistics
- `getMentoringStatistics()` - Mentoring averages per department
- `getMentoringStatisticsSorted()` - Sorted by total average (descending)

### Project Counts
- `getProjectsByAssignedDepartment()` - Projects by initial assignment
- `getProjectsByProfessorDepartment()` - Projects by professor departments
- `getProjectsBySupervisorDepartment()` - Projects by supervisor department

### Analysis
- `getDepartmentMismatches()` - Identifies assignment vs supervision discrepancies
- `getIntegrityIssues()` - Standardized issue detection
- `getWidgetStatistics()` - Widget-compatible data with calculation mode option

## Integration Points

1. **Integrity Check Command**: `/app/Console/Commands/CheckProjectStatisticsIntegrity.php`
2. **Dashboard Widget**: `/app/Filament/Administration/Widgets/Dashboards/DepartmentAgreementsStatsWidget.php`
3. **Filament Admin Page**: `/app/Filament/Administration/Pages/ProjectStatisticsIntegrityCheck.php`

## Business Logic Explanation

The discrepancy between "assigned department" and "professor department" counts occurs because:

1. **Initial Assignment**: Projects are assigned to departments for supervisor distribution
2. **Reviewer Assignment**: Two reviewers are then added, potentially from other departments
3. **Result**: Final project supervision involves professors from multiple departments

This is normal business logic but creates statistical complexity when calculating department workloads and mentoring averages.
