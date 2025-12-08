import { Button, Col, Container, Modal, Row } from "react-bootstrap";
import { useParams } from "react-router-dom";
import { useCallback, useEffect, useState } from "react";
import type Post from "../services/Post";
import PostFull from "../components/PostFull";
import PostService from "../services/PostService";
import RecentPosts from "../components/RecentPosts";

function PostPage() {

    const params = useParams() as { id: string }

    const [post, setPost] = useState<Post>();

    const [show, setShow] = useState(false);

    const populatePost = useCallback(() => {
        const service = new PostService();
        const postId = parseInt(params.id);
        const data = service.getById(postId);
        setPost(data);
    }, [params]);

    const deletePost = useCallback(() => {
        const service = new PostService();
        const postId = parseInt(params.id);
        service.delete(postId);
        window.navigate('/');
    }, [params]);

    useEffect(() => {
        populatePost();
    }, [populatePost]);

    const handleModalClose = () => setShow(false);
    const handleModalShow = () => setShow(true);

    return (
        <Container>

            <Row className="mt-4">
                <Col md="9">
                    <div className="d-flex justify-content-between mb-4">
                        <div>
                            <button onClick={() => window.navigate('/')} className="btn btn-outline-secondary btn-sm"><i className="bi bi-arrow-left"></i> Back</button>
                        </div>
                        <div>
                            <button className="btn btn-outline-danger btn-sm" onClick={handleModalShow}>Delete <i className="bi bi-trash"></i></button>
                        </div>
                    </div>

                    {post === undefined ? <div>Loading post</div> : <PostFull post={post} />}
                </Col>

                <Col md="3">
                    <RecentPosts />
                </Col>
            </Row>

            <Modal show={show} onHide={handleModalClose}>
                <Modal.Header closeButton>
                    <Modal.Title>Delete post</Modal.Title>
                </Modal.Header>
                <Modal.Body>Are you sure you want to delete the post?</Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={handleModalClose}>
                        No, keep it
                    </Button>
                    <Button variant="danger" onClick={deletePost}>
                        Yes, delete
                    </Button>
                </Modal.Footer>
            </Modal>

        </Container>

    );
}

export default PostPage;