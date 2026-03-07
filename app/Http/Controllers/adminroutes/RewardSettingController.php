<?php

namespace App\Http\Controllers\adminroutes;

use App\Models\RewardRule;
use Illuminate\Http\Request;

class RewardSettingController extends Controller
{
    /**
     * Display a listing of the reward rules.
     */
    public function index()
    {
        $rules = RewardRule::all();
        return view('settings.rewards', compact('rules'));
    }

    /**
     * Update the specified reward rules.
     */
    public function update(Request $request)
    {
        $request->validate([
            'rules' => 'required|array',
            'rules.*.coins' => 'required|integer|min:0',
            'rules.*.status' => 'boolean',
        ]);

        foreach ($request->rules as $id => $data) {
            $rule = RewardRule::find($id);
            if ($rule) {
                $rule->update([
                    'coins' => $data['coins'],
                    'status' => isset($data['status']),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Reward rules updated successfully.');
    }
}
