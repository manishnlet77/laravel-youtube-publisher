<!DOCTYPE array>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube Publisher Sandbox</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { border: 1px solid #ccc; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .btn { padding: 10px 15px; background: #ff0000; color: #fff; text-decoration: none; border-radius: 4px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>YouTube Publisher Sandbox 🚀</h1>
        <p>This is the testing sandbox for the <code>manishnlet77/laravel-youtube-publisher</code> package.</p>
        
        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="card">
            <h2>1. Authentication</h2>
            @if($hasToken)
                <p style="color: green;">✅ YouTube Account is Connected!</p>
                <a href="{{ route('youtube-publisher.auth') }}" class="btn" style="background: #333;">Reconnect YouTube</a>
            @else
                <p>Connect your YouTube account to get started.</p>
                <a href="{{ route('youtube-publisher.auth') }}" class="btn">Connect YouTube</a>
            @endif
        </div>

        @if($hasToken)
        <div class="card">
            <h2>2. Test Upload</h2>
            <form action="{{ route('youtube-publisher.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label>Video Title</label><br>
                    <input type="text" name="title" required style="width: 100%; padding: 8px;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label>Upload Type</label><br>
                    <select name="type" style="width: 100%; padding: 8px;">
                        <option value="video">Normal Video</option>
                        <option value="short">YouTube Short</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Video File (MP4)</label><br>
                    <input type="file" name="video" accept="video/mp4,video/quicktime" required>
                </div>

                <button type="submit" class="btn">Upload to YouTube</button>
            </form>
        </div>
        @endif
    </div>
</body>
</html>
