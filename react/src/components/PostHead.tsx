import type Post from '../services/Post';

function PostHead(props: { post: Post }) {

    return (
        <div className="post">
            <h2>{props.post.title}</h2>
            <p>{props.post.content.substring(0, 50) + " ..."} </p>
            <button className="btn btn-link p-0"
                    onClick={() => window.navigate(`/post/${props.post.id}`)}>
                Read more
            </button>
        </div>
    );
}

export default PostHead;