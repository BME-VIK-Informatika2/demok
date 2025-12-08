import Container from 'react-bootstrap/Container';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';
import { createBrowserRouter, RouterProvider } from 'react-router-dom';
import HomePage from './pages/HomePage';
import PostPage from './pages/PostPage';
import CreatePage from './pages/CreatePage';

declare global {
    interface Window { navigate: (path: string) => void; }
}

function App() {
    const prefix = "/demok/react/";
    const router = createBrowserRouter([
        {
            path: `${prefix}/`,
            element: <HomePage />
        },
        {
            path: `${prefix}/post/:id`,
            element: <PostPage />
        },
        {
            path: `${prefix}/new`,
            element: <CreatePage />
        }
    ]);

    window.navigate = (path:string) => {router.navigate(`${prefix}${path}`)};

    return (
        <div className="d-flex flex-column min-vh-100">

            <Navbar expand="md" className="bg-body-tertiary">
                <Container>
                    <Navbar.Brand onClick={() => window.navigate("/")} role="button">My Blog</Navbar.Brand>
                    <Navbar.Toggle aria-controls="basic-navbar-nav" />
                    <Navbar.Collapse id="basic-navbar-nav">
                        <Nav className="ms-auto">
                            <Nav.Link onClick={() => window.navigate("/new")}>New post</Nav.Link>
                        </Nav>
                    </Navbar.Collapse>
                </Container>
            </Navbar>

            <main className="flex-grow-1">
                <RouterProvider router={router} />
            </main>


            <footer className="py-4 text-center text-body-secondary bg-body-tertiary mt-4">
                Blog demo for <a href="https://portal.vik.bme.hu/kepzes/targyak/VIAUBXAV081-00" className="link-body-emphasis">BMEVIAUBXAV081</a>
            </footer>

        </div>
    );

}

export default App;