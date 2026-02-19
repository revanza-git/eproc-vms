import os
import shutil
import sys
import time
from urllib.request import urlopen

from PIL import Image


def backup_if_exists(path: str, suffix: str) -> None:
    if os.path.exists(path):
        shutil.copy2(path, f"{path}.bak.{suffix}")


def write_resized(src: Image.Image, dest: str, w: int, h: int, fmt: str, backup_suffix: str) -> None:
    backup_if_exists(dest, backup_suffix)
    os.makedirs(os.path.dirname(dest), exist_ok=True)

    scale = min(w / src.width, h / src.height)
    dw = max(1, round(src.width * scale))
    dh = max(1, round(src.height * scale))

    resized = src.resize((dw, dh), Image.Resampling.LANCZOS)

    if fmt.lower() == "png":
        canvas = Image.new("RGBA", (w, h), (0, 0, 0, 0))
        resized = resized.convert("RGBA")
        mask = resized
    else:
        canvas = Image.new("RGB", (w, h), (255, 255, 255))
        resized = resized.convert("RGB")
        mask = None

    x = (w - dw) // 2
    y = (h - dh) // 2
    canvas.paste(resized, (x, y), mask)

    if fmt.lower() == "png":
        canvas.save(dest, format="PNG", optimize=True)
    else:
        canvas.save(dest, format="JPEG", quality=92, optimize=True, progressive=True)


def main() -> int:
    backup_suffix = time.strftime("%Y%m%d%H%M%S")
    if len(sys.argv) >= 2 and sys.argv[1].strip():
        source_path = sys.argv[1]
        if not os.path.exists(source_path):
            raise FileNotFoundError(source_path)
    else:
        source_url = "https://nusantararegas.com/logo.png"
        temp_dir = os.environ.get("TEMP", os.getcwd())
        source_path = os.path.join(temp_dir, f"nr_logo_source_{backup_suffix}.png")

        with urlopen(source_url) as r, open(source_path, "wb") as f:
            f.write(r.read())

    src = Image.open(source_path)
    src.load()

    targets = [
        (r"c:\inetpub\eproc\intra\pengadaan\assets\images\login-regas-logo.png", 1023, 257, "png"),
        (r"c:\inetpub\eproc\intra\pengadaan\assets\images\login-regas-logo.jpg", 1023, 257, "jpg"),
        (r"c:\inetpub\eproc\intra\pengadaan\assets\images\login-regas-logo1.jpg", 1023, 257, "jpg"),
        (r"c:\inetpub\eproc\intra\pengadaan\assets\images\logo-nr.png", 2073, 642, "png"),
        (r"c:\inetpub\eproc\intra\pengadaan\assets\images\logo-nr_.png", 2073, 642, "png"),
        (r"c:\inetpub\eproc\vms\app\assets\images\login-regas-logo.png", 1023, 257, "png"),
        (r"c:\inetpub\eproc\vms\app\assets\images\login-regas-logo.jpg", 1023, 257, "jpg"),
        (r"c:\inetpub\eproc\vms\app\assets\images\login-regas-logo1.jpg", 1023, 257, "jpg"),
        (r"c:\inetpub\eproc\vms\app\assets\images\logo-nr.png", 2073, 642, "png"),
        (r"c:\inetpub\eproc\vms\app\assets\images\logo-nr_.png", 2073, 642, "png"),
        (r"c:\inetpub\eproc\intra\main\assets\images\logo-nr.png", 2073, 642, "png"),
        (r"c:\inetpub\eproc\intra\main\assets\images\NUSANTARA-REGAS-2.png", 1023, 257, "png"),
        (r"c:\inetpub\eproc\intra\main\assets\images\NUSANTARA-REGAS.png", 1858, 592, "png"),
        (r"c:\inetpub\eproc\intra\main\assets\lampiran\pr_lampiran\login-regas-logo.jpg", 1023, 257, "jpg"),
    ]

    for path, w, h, fmt in targets:
        write_resized(src, path, w, h, fmt, backup_suffix)

    print(f"updated={len(targets)}")
    print(f"backup_suffix={backup_suffix}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
