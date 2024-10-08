<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 
use App\Models\CTusers;
use App\Models\NhanVien;
use App\Models\Users;
use App\Traits\TrangThaiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    use TrangThaiTrait;
    /**
     * @OA\post(
     *     path="/api/taikhoan/search",
     *    tags={"taikhoan"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */

    public function search(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page');
        $totalPage = $request->input('pageSize');

        $query = Users::query();
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        $db = $query->paginate($totalPage ?? ($page ?? 1));

        return $db->total() > 0 ? $this->ok($db) : $this->errors(null);
    }



    /**
     * @OA\post(
     *     path="/api/taikhoan/signup",
     *    tags={"taikhoan"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function signup(Request $res)
    {

        // Kiểm tra xem tên tài khoản đã tồn tại chưa
        if (Users::where('name', $res->name)->exists()) {
            return $this->errors('Tên tài khoản đã tồn tại !');
        }

        // Kiểm tra xem email đã tồn tại chưa
        if (Users::where('email', $res->email)->exists()) {
            return $this->errors('Email đã tồn tại !');
        }

        // Thêm tài khoản mới vào cơ sở dữ liệu
        $tk = new Users();
        $tk->name = $res->name;
        $tk->email = $res->email;
        $tk->role = 2;
        $tk->password = Hash::make($res->MatKhau);
        $db = $tk->save();

        $ct = new CTusers();
        $ct->TaiKhoanID = $tk->id;
        $ct->HoVaTen = $tk->name;
        $ct->AnhDaiDien = "img_1.jpg";
        $ct->save();

        return $db ? $this->ok($db) : $this->errors(null);
    }

    /**
     * @OA\delete(
     *     path="/api/taikhoan/delete/{id}",
     *    tags={"taikhoan"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function delete($id)
    {
        $kt = Users::find($id);
        if ($kt['role'] !== 0) {
            $db = Users::where('id', $id)->first()->delete();
            return $db ? $this->ok($db) : $this->errors(null);
        } else {
            return $this->errors('Tài khoản này không thể xoá!');
        }
    }
    /**
     * @OA\delete(
     *     path="/api/taikhoan/deletes",
     *    tags={"taikhoan"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function deletes(Request $request)
    {
        $ids = $request->input('ids');
        $deletableIds = [];

        foreach ($ids as $id) {
            $user = Users::find($id);
            if ($user && $user->role !== 0) {
                $deletableIds[] = $id;
            }
        }

        if (empty($deletableIds)) {
            return $this->errors('Không có tài khoản nào có thể xoá!');
        }

        $db = Users::whereIn('id', $deletableIds)->delete();

        return $db ? $this->ok($db) : $this->errors(null);
    }


    /**
     * @OA\post(
     *     path="/api/taikhoan/save/{id}",
     *    tags={"taikhoan"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function save(Request $res)
    {
        $id = $res->id;


        $tk = $id ? Users::find($id) : new Users();
        if (!$id) {
            // Kiểm tra xem tên tài khoản đã tồn tại chưa
            if (Users::where('name', $res->name)->exists()) {
                return $this->errors('Tên tài khoản đã tồn tại !');
            }

            // Kiểm tra xem email đã tồn tại chưa
            if (Users::where('email', $res->email)->exists()) {
                return $this->errors('Email đã tồn tại !');
            }

            $tk->name = $res->name;
        }

        $tk->email = $res->email;

        if ($res->pass) {
            $tk->password = Hash::make($res->pass);
        }

        $tk->role = intval($res->role);


        $db = $tk->save();

        if ($res->role !== "2" && !$id) {
            $nv = new NhanVien();
            $nv->TaiKhoanID = $tk->id;
            $nv->TenNhanVien = $tk->name;
            $nv->save();
        }

        if (!$id) {
            $ct = new CTusers();
            $ct->TaiKhoanID = $tk->id;
            $ct->HoVaTen = $tk->name;
            $ct->AnhDaiDien = "img_1.jpg";
            $ct->save();
        }


        return $db ? $this->ok($tk) : $this->errors(null);
    }

    /**
     * @OA\post(
     *     path="/api/taikhoan/changepassword",
     *    tags={"taikhoan"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function changePassword(Request $res)
    {
        $db = Users::where('id', $res->id)->first();

        if (!$db) {
            return response()->json(['error' => 'Người dùng không tồn tại.'], 404);
        }

        if (!Hash::check($res->password, $db->password)) {
            return response()->json(['error' => 'Mật khẩu cũ không khớp!'], 400);
        }
        $db->password = Hash::make($res->confirmPassword);

        return $db->save() ? $this->ok("Đổi mật khẩu thành công.") : $this->errors(null);
    }

    /**
     * @OA\post(
     *     path="/api/taikhoan/updatectusers/{id}",
     *    tags={"taikhoan"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function updateCTusers(Request $request, $id)
    {

        $ct =  CTusers::where('TaiKhoanID', $id)->first();
        $ct->HoVaTen = $request->HoVaTen;
        $ct->DiaChi = $request->DiaChi;
        $ct->SDT = $request->SDT;
        $ct->CMND = $request->CMND;
        $file_name = $this->uploadFile($request, 'upload_file', 'TaiKhoan');
        if ($file_name !== null) {
            $ct->AnhDaiDien = 'TaiKhoan/' . $file_name;
        }

        return  $ct->save() ? $this->ok($ct) : $this->errors(null);
    }
    /**
     * @OA\Get(
     *     path="/api/taikhoan/gettaikhoanct/{id}",
     *    tags={"taikhoan"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function getTaiKhoanCT($id)
    {
        $db = DB::table('users')
            ->join('ctusers', 'users.id', '=', 'ctusers.TaiKhoanID')
            ->where('users.id', $id)
            ->select('users.*', 'ctusers.*')
            ->first();

        return $db ? $this->ok($db) : $this->errors(null);
    }

    /**
     * @OA\Get(
     *     path="/api/taikhoan/get/{id}",
     *    tags={"taikhoan"},
     *     @OA\Response(response="200", description="Success"),
     * )
     */
    public function getTaiKhoan($id)
    {
        $db = Users::find($id);
        return $db ? $this->ok($db) : $this->errors(null);
    }
    public function getCTTaiKhoan($id)
    {
        $db = CTusers::where("TaiKhoanID", $id)->first();
        return $db ? $this->ok($db) : $this->errors(null);
    }
}
