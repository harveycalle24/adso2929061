<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome MVC</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500 min-h-screen">

<div class="hero min-h-screen bg-opacity-20 bg-black">
  <div class="hero-content text-center">
    <div class="max-w-md p-6 rounded-2xl shadow-xl bg-white/20 backdrop-blur-xl text-white">

      <h1 class="text-5xl font-bold text-yellow-300 drop-shadow-lg">MVC</h1>
      <h3 class="mb-10 text-lg opacity-90">Model View Controller</h3>

      <a href="index.php?method=create" class="btn btn-warning my-4 shadow-lg">
        Add Pokemon
      </a>

      <!-- Tabla -->
      <div class="overflow-x-auto rounded-xl border border-white/20 bg-white/10 backdrop-blur-lg mt-4">
        <table class="table text-white">
          <thead class="text-yellow-300">
            <tr>
              <th>Id</th>
              <th>Name</th>
              <th>Type</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as $pokemon): ?>
            <tr class="hover:bg-white/10">
              <th><?=htmlspecialchars($pokemon['id'])?></th>
              <td><?=htmlspecialchars($pokemon['name'])?></td>
              <td>
                <?php if($pokemon['type'] === 'Water'):?>
                  <span class="badge badge-info badge-lg">Water</span>
                <?php elseif($pokemon['type'] === 'Grass'):?>
                  <span class="badge badge-success badge-lg">Grass</span>
                <?php elseif($pokemon['type'] === 'Fire'):?>
                  <span class="badge badge-error badge-lg">Fire</span>
                <?php elseif($pokemon['type'] === 'Electric'):?>
                  <span class="badge badge-warning badge-lg">Electric</span>
                <?php elseif($pokemon['type'] === 'Normal'):?>
                  <span class="badge badge-neutral badge-lg">Normal</span>
                <?php endif?>
              </td>

              <td class="flex gap-2">
                <a href="index.php?method=show&id=<?= $pokemon['id'] ?>" class="btn btn-xs btn-info"></a>
                <a href="index.php?method=edit&id=<?= $pokemon['id'] ?>" class="btn btn-xs btn-secondary">editar</a>
                <a href="index.php?method=delete&id=<?= $pokemon['id'] ?>" class="btn btn-xs btn-error">eliminar</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <p class="py-6 opacity-80">
        Gestiona tus Pokémon de manera sencilla usando MVC.
      </p>

      <button class="btn btn-primary shadow-lg">Get Started</button>

    </div>
  </div>
</div>
</body>
</html>