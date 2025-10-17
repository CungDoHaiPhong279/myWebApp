# Project: NoteApp
Giới thiệu:

**Họ và tên Sinh viên:** Cung Đỗ Hải Phong 

**Mã Sinh viên:** 23010341 

**Lớp:** CSE702051-1-1-25(COUR01.TH5)  

## 📝 Mô tả dự án

Website quản lý ghi chú, cho phép người dùng tạo ghi chú, phân loại.  

## 🧰 Công nghệ sử dụng
- PHP (Laravel Framework)
- Laravel Breeze
- MySQL (Aiven Cloud)
- Blade Template
- Tailwind CSS (do Breeze tích hợp sẵn)


# Sơ đồ khối
![SQL diagram](https://github.com/user-attachments/assets/8975bb23-d763-4111-884e-94f9c8dd20d6)


## Sơ đồ chức năng

![UML](https://github.com/user-attachments/assets/479e08ad-43ed-4007-9419-98ee520148e2)
  


## Sơ đồ thuật toán

<strong>CRUD Note</strong>  

![Sequence](https://github.com/user-attachments/assets/b6eb937d-9317-4f8d-ad5b-39756c5c2f6e)
  


# Một số Code chính minh họa

## Model

<strong>Note Model</strong>

```php
class Note extends Model
{
     protected $fillable = ['title', 'text', 'user_id', 'status_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
   }
}

```
<strong>Tag Model</strong>

```php
class Tag extends Model
{
    protected $fillable = ['tagName'];

    public function notes() {
        return $this->belongsToMany(Note::class);
    }

}

```
<strong>Status Model</strong>

```php
class Status extends Model
{
    protected $fillable = ['name'];

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}

```


## Controller
<strong>Notes Controller</strong>

```php
class NotesController extends Controller
{

    //search
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Note::where('user_id', $user->id);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('tags', function ($q2) use ($searchTerm) {
                        $q2->where('tagName', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        $notes = $query->with(['tags', 'status'])->paginate(10);

        return view('notes.index', ['notes' => $notes]);
    }

    //get
    public function note($id)
    {
        $user = Auth::user();
        $note = Note::where('id', $id)
            ->where('user_id', $user->id)
            ->with('tags', 'status')
            ->firstOrFail();

        return view('notes.note', [
            'note' => $note
        ]);
    }

    //create
    public function create()
    {
        $statuses = Status::all();
        return view('notes.form', compact('statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:50',
            'text' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'status_id' => 'required|exists:statuses,id',
        ]);

        $note = Note::create([
            'title' => $validated['title'],
            'text' => $validated['text'],
            'user_id' => auth()->id(),
            'status_id' => $validated['status_id'],
        ]);

        if ($request->filled('tags')) {
            $tagNames = array_map('trim', explode(',', $request->input('tags')));
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(['tagName' => $tagName]);
                    $tagIds[] = $tag->id;
                }
            }
            $note->tags()->sync($tagIds);
        }

        return redirect()->route('notes.index')->with('success', 'Note created successfully.');
    }

    //update
    public function edit($id)
    {
        $user = Auth::user();
        $note = Note::where('id', $id)
            ->where('user_id', $user->id)
            ->with('tags')
            ->firstOrFail();

        $statuses = Status::all();

        return view('notes.edit', compact('note', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:50',
            'text' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'status_id' => 'required|exists:statuses,id',
        ]);

        $user = Auth::user();
        $note = Note::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $note->title = $request->input('title');
        $note->text = $request->input('text');
        $note->status_id = $request->input('status_id');
        $note->save();

        $tagIds = [];
        if ($request->filled('tags')) {
            $tagNames = array_map('trim', explode(',', $request->input('tags')));
            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(['tagName' => $tagName]);
                    $tagIds[] = $tag->id;
                }
            }
        }
        $note->tags()->sync($tagIds);

        return redirect()->route('notes.index')->with('success', 'Note updated successfully!');
    }

    //delete
    public function delete($id)
    {
        $user = Auth::user();
        $note = Note::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $note->delete();
        return redirect()->route('notes.index')->with('success', 'Note deleted successfully!');
    }
}

```

## View

<strong>
    Cấu trúc chính của view
</strong>

![view](https://github.com/user-attachments/assets/4985732e-9ee1-4b80-8622-459b153afa65)


<strong>
    Sử dụng thư viện Tailwind CSS
</strong>

![taiwind](https://github.com/user-attachments/assets/4c4dcada-0b8c-4e6e-b1ea-bc804efdc923)


# Security Setup

<strong>
    Sử dụng @csrf để chống tấn công CSRF
</strong>

![csrf](https://github.com/user-attachments/assets/96534d28-6baa-40b0-9875-11305fadaa3e)


<strong>
    Chống XSS
</strong>

![xss](https://github.com/user-attachments/assets/e91260e3-283c-4f1a-8e1c-ee5e192ea6a2)


<strong>
    Validation: Ràng buộc dữ liệu
    Ví dụ method NotesController@store
</strong>

![validate](https://github.com/user-attachments/assets/9c33cc02-b175-4e3d-93c0-a78226669cf4)


<strong>
    Query Builder Protection chống SQL Injection
</strong>

![query](https://github.com/user-attachments/assets/59ae0083-c6b0-4731-ba4f-0e07869d91c4)


<strong>
    Middleware bảo mật
    Xử dụng các middleware auth của laravel
    Ví dụ: file routes/web.php
</strong>

![middleware](https://github.com/user-attachments/assets/7fb4af65-f8f1-42b4-9b29-a5bd1c2096c5)


<strong>
    Authorization
method: NotesController@update
</strong>

![auth](https://github.com/user-attachments/assets/5eca7bac-7bbb-4c62-bb70-c2493ae12dd9)


<strong>
    Authentication
    Ví dụ: Sử dụng Auth() để lấy thông tin user 1 cách an toàn
</strong>

![authen](https://github.com/user-attachments/assets/8ff49057-3474-45ed-bb1a-b99efbd83c76)


# Link

## Github Link

`https://github.com/CungDoHaiPhong279/myWebApp.git`

## Github page

`https://CungDoHaiPhong279.github.io/NoteApp_Laravel/`

## Public Web (deployment) link: 
`https://noteapp-laravel-main-3mubbt.laravel.cloud/`

# Một số hình ảnh chức năng chính

## Xác thực người dùng <\<Breeze>\>

<strong>Trang đăng nhập</strong>

![sign-in](https://github.com/user-attachments/assets/f6042fd5-63a8-4a21-beea-782aa387ec7b)


<strong>Trang đăng ký</strong>

![register](https://github.com/user-attachments/assets/85b94dc0-0c5e-4b64-9b51-6b6dffff68c1)


## Trang chính

![trangchu](https://github.com/user-attachments/assets/78f8172d-c90a-449f-ba65-8864937a2b2f)


## CRUD Note

<strong>Create Note</strong>

![create](https://github.com/user-attachments/assets/6e56ee22-ae91-4a38-b87a-3ae025af5178)


<strong>Read</strong>

![read](https://github.com/user-attachments/assets/7f8cf7bc-a39f-4cd6-90af-d971ae158330)


<strong>Update</strong>

![update](https://github.com/user-attachments/assets/d5a8518e-14fc-4faf-9507-a273e2d9e2ce)


<strong>Delete</strong>

![delete](https://github.com/user-attachments/assets/a9040032-421b-4cd8-8661-32d83537b74d)


<strong>Search</strong>

![search](https://github.com/user-attachments/assets/dde4474c-74d5-46be-a0ff-92c5fde2c093)


# License & Copy Rights

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
