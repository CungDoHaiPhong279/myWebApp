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

(![Sequence](https://github.com/user-attachments/assets/b6eb937d-9317-4f8d-ad5b-39756c5c2f6e)
  


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

(<img width="260" height="491" alt="image" src="https://github.com/user-attachments/assets/f6063f89-bca2-40e4-b199-d7c543bf9a4b" />



<strong>
    Sử dụng thư viện Tailwind CSS
</strong>

<img width="471" height="292" alt="image" src="https://github.com/user-attachments/assets/6aeec018-ab7b-477e-9c47-50190df56962" />


# Security Setup

<strong>
    Sử dụng @csrf để chống tấn công CSRF
</strong>

<img width="1036" height="253" alt="image" src="https://github.com/user-attachments/assets/ac0be449-7605-47fa-aae4-3459bb3faa27" />


<strong>
    Chống XSS
</strong>

<img width="1136" height="411" alt="image" src="https://github.com/user-attachments/assets/0db5dac3-9707-4932-989b-4196d7ed4709" />


<strong>
    Validation: Ràng buộc dữ liệu
    Ví dụ method NotesController@store
</strong>

<img width="1132" height="390" alt="image" src="https://github.com/user-attachments/assets/24f335ea-c267-46c0-9757-5c2762e80f36" />


<strong>
    Query Builder Protection chống SQL Injection
</strong>

<img width="624" height="297" alt="image" src="https://github.com/user-attachments/assets/d5a453e1-395d-40ae-93ba-9b1dd98c8bba" />


<strong>
    Middleware bảo mật
    Xử dụng các middleware auth của laravel
    Ví dụ: file routes/web.php
</strong>

<img width="1024" height="433" alt="image" src="https://github.com/user-attachments/assets/05a964bc-80ba-4d8f-87b4-6e08b9839ddf" />


<strong>
    Authorization
method: NotesController@update
</strong>

<img width="573" height="132" alt="image" src="https://github.com/user-attachments/assets/b5187067-3cbb-425f-a61a-1cb7dec32785" />


<strong>
    Authentication
    Ví dụ: Sử dụng Auth() để lấy thông tin user 1 cách an toàn
</strong>


<img width="789" height="174" alt="image" src="https://github.com/user-attachments/assets/94ef45aa-cd30-4411-9850-a8c203e26eb3" />

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

<img width="1920" height="986" alt="image" src="https://github.com/user-attachments/assets/3236570e-bb24-42fd-bc15-a9c6e4efa91c" />

<strong>Trang đăng ký</strong>

<img width="1920" height="885" alt="image" src="https://github.com/user-attachments/assets/e41c04ca-36d5-4d2e-80ee-26043981dde1" />

## Trang chính

<img width="1920" height="888" alt="image" src="https://github.com/user-attachments/assets/f2c821d9-5cc6-40f7-9f76-3ecd6891fcf0" />

## CRUD Note

<strong>Create Note</strong>

<img width="1920" height="881" alt="image" src="https://github.com/user-attachments/assets/abd5beaf-4a7c-4bd6-a0b4-854e0415c426" />

<strong>Read</strong>

<img width="1920" height="878" alt="image" src="https://github.com/user-attachments/assets/64458aec-724b-4cb8-9b94-dce21f24f437" />

<strong>Update</strong>

<img width="1920" height="884" alt="image" src="https://github.com/user-attachments/assets/df901aa6-c802-4a46-9ea4-badd40ce1dcd" />

<strong>Delete</strong>

<img width="1920" height="981" alt="image" src="https://github.com/user-attachments/assets/bbf0ab53-7381-4ee8-8172-ea395f2345b4" />

<strong>Search</strong>

<img width="1920" height="878" alt="image" src="https://github.com/user-attachments/assets/80a77025-31b3-45dd-8328-4d5c474154ed" />

# License & Copy Rights

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
