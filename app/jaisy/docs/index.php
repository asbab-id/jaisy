<?php

if(isset($versi)){
    $versi = $versi;
}else{
    $versi = '0.0';
}


if(file_get_contents(__DIR__.'/v'.$versi.'.html') ){
    $content =  file_get_contents(__DIR__.'/v'.$versi.'.html');
}else{
    header("Location: /docs");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation Jaisy v<?php echo $versi; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        .judul-chapter{
            font-size: 3rem;
        }

        .keterangan-chapter{
            font-size: 1.5rem;
        }

        .pre{
            white-space: pre-line;
        }

        .justify{
            text-align: justify;
        }

        @media (min-width: 768px) {
            .sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-primary text-white p-4">
        <div class="container d-flex align-items-center">
           <img src="static/logo.png" width="30">
            <h1 class="h2 ms-3">Documentation Jaisy v<?php echo $versi; ?></h1>
            <button class="btn btn-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle sidebar">
                ☰
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-4 col-lg-2 d-md-block bg-light sidebar collapse overflow-scroll">
                <div class="position-sticky">
                    <h2 class="h5 px-3 py-4 border-bottom">Navigation</h2>
                    <ul class="nav flex-column" id="nav">
                        <!-- $nav -->
                    </ul>
                </div>
            </nav>

            <main class="col-md-8 ms-sm-auto col-lg-10 px-md-4">
                <!-- $content -->
                <?php echo $content;?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>





<script>
const classesToAdd = {
    'chapter': 'pt-4 pb-2 border-bottom border-primary',
    'judul-chapter': 'h3',
    'section': 'pt-4 pb-2',
    'judul-section': 'h4',
    'content-section': 'justify',
    'code-section': 'bg-light p-3 rounded border border-dark',
    'code-output': 'p-3 bg-primary-subtle pre',
    'penjelasan-section': 'justify pre text-secondary'
};

document.addEventListener('DOMContentLoaded', function() {
    addClass(classesToAdd);
    renderNav();
    renderSyntaxHighlight();
    trimPre();
});

function renderNav() {
    let chapters = document.querySelectorAll('.chapter');
    let navElement = document.getElementById('nav');
    let listNav = [];

    chapters.forEach((chapter, i) => {
        let judulChapterElement = chapter.querySelector('.judul-chapter');
        if (judulChapterElement) {
            let judulChapter = judulChapterElement.innerText;
            let judulChapterId = judulChapter.replace(/\s+/g, '-').toLowerCase();
            chapter.id = judulChapterId;

            let sections = chapter.querySelectorAll('.section');
            let sectionList = [];

            let sectionLinks = '';

            sections.forEach((section, j) => {
                let judulSectionElement = section.querySelector('.judul-section');
                if (judulSectionElement) {
                    let judulSection = judulSectionElement.innerText;
                    let judulSectionId = judulSection.replace(/\s+/g, '-').toLowerCase();
                    section.id = judulSectionId;

                    sectionList.push({ id: judulSectionId, title: judulSection });

                    sectionLinks += `
                        <li class="nav-item">
                            <a class="nav-link" href="#${judulSectionId}">${judulSection}</a>
                        </li>
                    `;
                }
            });

            listNav.push({
                chapterId: judulChapterId,
                chapterTitle: judulChapter,
                sections: sectionList
            });

            let chapterHtml = `
                <li class="nav-item">
                    <a class="nav-link bg-primary-subtle" href="#${judulChapterId}">${judulChapter}</a>
                    <ul class="nav flex-column ms-3">
                        ${sectionLinks}
                    </ul>
                </li>
            `;

            navElement.innerHTML += chapterHtml;
        }
    });

    console.log(listNav);
}

function addClass(classArray) {
    for (let key in classArray) {
        if (classArray.hasOwnProperty(key)) {
            let elements = document.querySelectorAll(`.${key}`);
            let classesToAdd = classArray[key].split(' ');

            elements.forEach(element => {
                element.classList.add(...classesToAdd);
            });
        }
    }
}

function renderSyntaxHighlight(){
    let code = document.querySelectorAll('code');

    Array.from(code).forEach(element => {
        staticHighlight(element);
    })
}

function trimPre(){
    let pre = document.querySelectorAll('.pre');

    Array.from(pre).forEach(element => {
        element.innerText = element.innerText.trim();
    })
}
</script>
<script src="static/jaisy-static-highlight.js"></script>





</body>
</html>
