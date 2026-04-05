<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class ManajemenAdmin extends Component
{
    use WithPagination;

    public $showModal = false;

    public $editMode = false;

    public $adminId = null;

    public $nama = '';

    public $username = '';

    public $email = '';

    public $password = '';

    public $role = 'operator';

    public $is_active = true;

    protected $rules = [
        'nama' => 'required|string|max:150',
        'username' => 'required|string|max:100|unique:admins',
        'email' => 'required|email|max:150|unique:admins',
        'password' => 'required|string|min:6',
        'role' => 'required|in:super_admin,operator',
        'is_active' => 'boolean',
    ];

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->editMode = false;
    }

    public function openEditModal($id)
    {
        $admin = Admin::findOrFail($id);

        if ($admin->id === auth('admin')->id()) {
            session()->flash('error', 'Tidak dapat mengedit akun sendiri di sini');

            return;
        }

        $this->adminId = $id;
        $this->nama = $admin->nama;
        $this->username = $admin->username;
        $this->email = $admin->email;
        $this->password = '';
        $this->role = $admin->role;
        $this->is_active = $admin->is_active;
        $this->showModal = true;
        $this->editMode = true;

        $this->rules['username'] = 'required|string|max:100|unique:admins,username,'.$id;
        $this->rules['email'] = 'required|email|max:150|unique:admins,email,'.$id;
        $this->rules['password'] = 'nullable|string|min:6';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->adminId = null;
        $this->nama = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'operator';
        $this->is_active = true;
        $this->resetErrorBag();

        $this->rules['username'] = 'required|string|max:100|unique:admins';
        $this->rules['email'] = 'required|email|max:150|unique:admins';
        $this->rules['password'] = 'required|string|min:6';
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if (! $this->editMode || ($this->editMode && $this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editMode) {
            Admin::findOrFail($this->adminId)->update($data);
            session()->flash('success', 'Admin berhasil diperbarui');
        } else {
            Admin::create($data);
            session()->flash('success', 'Admin berhasil dibuat');
        }

        $this->closeModal();
    }

    public function toggleActive($id)
    {
        $admin = Admin::findOrFail($id);

        if ($admin->id === auth('admin')->id()) {
            session()->flash('error', 'Tidak dapat menonaktifkan akun sendiri');

            return;
        }

        $admin->update(['is_active' => ! $admin->is_active]);
        session()->flash('success', 'Status admin berhasil diubah');
    }

    public function render()
    {
        $admins = Admin::orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.admin.manajemen-admin', [
            'admins' => $admins,
        ])->layout('layouts.admin', ['title' => 'Manajemen Admin']);
    }
}
