const darkToggle = document.querySelector('#darktoggle');

darkToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');

    if (document.body.classList.contains('dark-mode')) {

        document.body.style.backgroundColor = '#111827';
        document.body.style.color = 'white';

        document.querySelector('nav').style.backgroundColor = '#111827';

        const home = document.querySelector('#Home');
        home.style.backgroundColor = '#1f2937';

        const pages = document.querySelector('#pages');
        pages.style.backgroundColor = '#171b3b';

        const courses = document.querySelector('#courses');
        courses.style.backgroundColor = '#1f2937';

        document.querySelectorAll('.shadow-lg').forEach(card => {
            card.style.backgroundColor = '#515c6d';
            card.style.color = 'white';
        });

    } else {

        document.body.style.backgroundColor = '';
        document.body.style.color = '';

        document.querySelector('nav').style.backgroundColor = '';

        const home = document.querySelector('#Home');
        home.style.backgroundColor = '';

        const pages = document.querySelector('#pages');
        pages.style.backgroundColor = '';

        const courses = document.querySelector('#courses');
        courses.style.backgroundColor = '';

        document.querySelectorAll('.shadow-lg').forEach(card => {
            card.style.backgroundColor = '';
            card.style.color = '';
        });
    }
});