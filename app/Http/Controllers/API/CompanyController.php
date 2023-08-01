<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
    public function fetch(Request $request)
    {
        // Parameter
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $companyQuery = Company::with(['users'])->whereHas('users', function ($query) {
            $query->where('user_id', Auth::id());
        });

        // Get Single Data
        if ($id) {
            $company = $companyQuery->find($id);

            if ($company) {
                return ResponseFormatter::success(
                    $company,
                    'Company found'
                );
            }
                return ResponseFormatter::error(
                    'Company not found',
                    404
                );
            
        }

        // Get Multiple Data
        $companies = $companyQuery;

        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Companies found'
        );
    }

    public function create(CreateCompanyRequest $request)
    {
        try {

            // Upload Logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            } else {
                $path = null;
            }

            // Create Company
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path,
            ]);

            if (!$company) {
                throw new \Exception('Failed to create company');
            }

            // Attach Company to User
            $user = User::find(Auth::id());
            $user->companies()->attach($company->id);

            // Load User at Company
            $company->load('users');

            return ResponseFormatter::success($company, 'Company Created');
        } catch (\Exception $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }

    public function update(UpdateCompanyRequest $request)
    {
        try {

            // Get Company
            $company = Company::find($request->id);

            // Check if company exists
            if (!$company) {
                throw new \Exception('Company Not Found');
            }

            // Upload Logo
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            // Update Company
            $company->update([
                'name' => $request->name,
                'logo' => $path,
            ]);

            // Load User at Company
            $company->load('users');

            return ResponseFormatter::success($company, 'Company Updated');
        } catch (\Exception $th) {
            return ResponseFormatter::error($th->getMessage(), 500);
        }
    }
}
